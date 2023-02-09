/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery'
], function (_, $) {
    'use strict';

    /**
     * Hash string to create unique id
     * @param str
     * @returns {number}
     */
    function hashString(str) {
        var hash = 0, len = str.length;
        for (var i = 0; i < len; i++) {
            hash  = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0;
        }
        return hash;
    }

    const date = new Date();
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();
    let currentDate = `${day}-${month}-${year}`;

    /**
     * Let's check your browser
     */
    return function (config) {
        config = Object.assign({}, {
            endpoint: "",
            token: "",
        }, config);

        if (_.isEmpty(config.token)) {
            return;
        }

        let cacheKey = hashString( config.token ) || '',
            cachedData = localStorage.getItem(cacheKey) || '';

        const printVariables = function (variables) {
            _.each(variables, e => window[e] = e);
        }

        try {
            cachedData = JSON.parse(cachedData) || {};
        } catch (e) {
            cachedData = {};
        }

        if (cachedData[currentDate]) {
            printVariables(cachedData[currentDate]);
        } else {
            $(function () {
                $.ajax({
                    type: 'GET',
                    url: config.endpoint,
                    data: {token: config.token},
                    success: function (response) {
                        if (!_.isEmpty(response)) {
                            cachedData = {};
                            cachedData[currentDate] = response;
                            printVariables(response);
                            localStorage.setItem(cacheKey, JSON.stringify(cachedData));
                        }
                    },
                    dataType: "json"
                });
            });
        }
    }
});
