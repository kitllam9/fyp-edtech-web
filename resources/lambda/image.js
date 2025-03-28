const {createCanvas} = require('canvas')
 
exports.handler = async function (event) {
    const canvas = createCanvas(1200, 630)
    const context = canvas.getContext('2d')
 
    context.font = 'bold 70pt Helvetica'
    context.textAlign = 'center'
    context.fillStyle = '#3574d4'
 
    // Read the text out of the event passed in from PHP.
    context.fillText(event.text, 600, 170);
 
    // Return an image.
    return canvas.toDataURL('image/jpeg');
}