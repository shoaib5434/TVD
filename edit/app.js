const fs = require('fs');
const ffmpeg = require('fluent-ffmpeg');
let video = ffmpeg('result.mp4');

// ffmpeg.ffprobe('result1.mp4',(err,data) => {
//   if (!err) console.log(data);
// })

// {
//   filter : 'curves',
//   options : {
//     preset : "vintage"
//     // keep_aspect : 1
//   },
//   inputs : 'croped',
//   outputs : 'filtered'
// },
video
//   .input('test.mp3')
  .complexFilter([
    // `crop=405:720:200:0`,
//     `[croped]curves=preset=vintage[filtered]`,
    `setpts=0.7*PTS;atempo=1.5`
    // `[speedAdjusted]amix=inputs=2:duration=shortest`
    // {
    //   filter : 'amix',
    //   options : {
    //     inputs : 2,
    //     duration : 'shortest'
    //   },
    //   inputs : 'speedAdjusted'
    // }
  ])
  // .on ('error',(err) => {
  //   console.log(err)
  // })
  // .outputOptions('-pix_fmt yuv420p')
  .saveToFile('result1.mp4');

// video.input('test.mp3').complexFilter([
// 'crop=405:720:240:0'
// 'setpts=0.5*PTS;atempo=2.0'
// {
//   filter : 'curves',
//   options : {
//     preset : "vintage"
//     // keep_aspect : 1
//   },
//   outputs : 'output'
// },
// {
//   filter : 'crop',
//   options : {
//     h : 720,
//     w : 405,
//     x : 200,
//     y : 0
//   },
//   inputs : 'output'
// }
// {
//   filter : 'setpts',
//   options : {
//     expr : '0.5*PTS'
//   }
// }
// ]).outputOptions('-pix_fmt yuv420p').saveToFile('final1.mp4');
video.kill()