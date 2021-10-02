# Compile your own assets resource

The module use the `SCSS` files as a styling material, and use `Grunt` to compile `SCSS` to `CSS` files

## 1. Install package via `npm`

    npm install

## 2. Install Grunt CLI

Official document can be found here [https://gruntjs.com/using-the-cli](https://gruntjs.com/using-the-cli)

    sudo npm install -g grunt-cli

## 3. Run command to compile now

Make your `SCSS` customization under [assets](https://github.com/Goomento/PageBuilder/tree/master/dev/assets/scss) folder, then run

    grunt build

Should run command in [dev](https://github.com/Goomento/PageBuilder/tree/master/dev) folder (same level with [Gruntfile.js](https://github.com/Goomento/PageBuilder/blob/master/dev/Gruntfile.js))
