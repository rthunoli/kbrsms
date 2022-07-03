const { reduce } = require('lodash');

Split = require('split-grid');

Split({
    columnGutters: [{
        track: 2,
        element: document.querySelector('.gutter-col-1'),
    }],
})

let element = document.querySelector('.gutter-col-1');
console.log(element);
// element. .attributes('background-color') = red;
