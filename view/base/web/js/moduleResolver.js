/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([], function () {
    /**
     *
     * @param callback
     * @param errback
     */
    const moduleResolver = function (callback, errback) {
        moduleResolver.resolveUnderscore(callback, errback);
    };

    /**
     * Compare version
     * @param a
     * @param b
     * @return {number}
     */
    moduleResolver.versionCompare = function (a, b) {
        return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
    };

    /**
     * Replace the package within requireJs
     * @param packageName
     * @param path
     */
    moduleResolver.replacePackage = function (packageName, path) {
        require.undef(packageName);
        if (path) {
            let paths = {};
            paths[packageName] = path;
            require.config({paths});
        }
    };

    /**
     * Replace the underscore
     *
     * @param callback
     * @param errback
     */
    moduleResolver.resolveUnderscore =  function (callback, errback) {
        require(['underscore'], function (_) {
            if (this.versionCompare(_.VERSION, '1.8.3') < 0) {
                this.replacePackage('underscore', 'Goomento_PageBuilder/lib/underscore/underscore');
                require(['underscore'], callback, errback);
            } else {
                callback();
            }
        }.bind(this), errback);
    };

    /**
     * Replace jQuery UI
     * @param callback
     * @param errback
     */
    moduleResolver.resolveJquery =  function (callback, errback) {
        require(['jquery', 'jquery/ui'], function ($) {
            if (this.versionCompare($.ui.version, '1.12.1') < 0) {
                this.replacePackage('jquery/ui', 'Goomento_PageBuilder/lib/jquery/jquery-ui.min');
                this.replacePackage('jquery/jquery-ui-1.9.2', 'jquery/ui');
                require(['jquery', 'jquery/ui'], callback, errback);
            } else {
                callback();
            }
        }.bind(this), errback);
    };

    return moduleResolver;
});
