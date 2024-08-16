let express = require('express');
let cors = require('cors');
const ffmpeg = require('fluent-ffmpeg');

let app = express();
app.use(cors())
app.use(express.json());

const getVideo = (url) => {
	return ffmpeg(url);
}


app.get('/info', async (req,res) => {
	let result = {};
	console.log(req.query.url);
	ffmpeg.ffprobe(req.query.url,(err,data) => {
		if (!err && data.streams.length) {
			result['height'] = data.streams[0].height;
			result['width'] = data.streams[0].width;
			result['duration'] = data.format.duration;
			result['size'] = data.format.size;
			res.status(200).json(result)
		}
		else {
			res.status(404).json({
				message : "Error"
			})
		}
	})
})

app.post('/crop',async (req,res) => {
	console.log(req.body)
	let video = getVideo(req.body.directory + '/' + req.body.editfile);
	let crop = `crop=${req.body.width}:${req.body.height}:${req.body.x}:${req.body.y}`;
	await video.complexFilter([
		crop
	]).saveToFile(req.body.directory + '/' + req.body.filename + '.mp4').
	on('end', () => {
		res.status(200).json({
			success : true,
			filename : req.body.filename + '.mp4'
		});
		console.log("Done");
	}).
	on("error", (err) => {
		console.log(err)
		res.status(200).json({
			success : false
		})
	})
})

app.post('/adjust', async (req,res) => {
	let video = getVideo(req.body.directory + '/' + req.body.editfile);
	console.log(req.body)
	let editString = ``, videoSpeed,audioSpeed;
	if (req.body.isSpeed) {
		if (req.body.speed == 1.5) {
			videoSpeed = 0.7;
			audioSpeed = 1.5;
		}
		else if (req.body.speed == 1) {
			videoSpeed = 1;
			audioSpeed = 1;
		}
		else if (req.body.speed == 0.5) {
			videoSpeed = 2;
			audioSpeed = .5;
		}
		else if (req.body.speed == 2) {
			videoSpeed = .5;
			audioSpeed = 2;
		}
		editString += `${(req.body.isFilter) ? `[filtered]` : `` }setpts=${videoSpeed}*PTS;atempo=${audioSpeed}[speedAdjusted]`;
	}
	editString = editString.substring(0,editString.lastIndexOf('['))
	console.log(editString)
	await video.complexFilter([
		editString
	]).saveToFile(req.body.directory + '/' + req.body.filename + '.mp4').
	on('end', () => {
		res.status(200).json({
			success : true,
			filename : req.body.filename + '.mp4'
		});
		console.log("Done");
	}).
	on("error", (err) => {
		console.log(err)
		res.status(200).json({
			success : false
		})
	});
})

app.post("/music", async (req,res) => {
	let video = getVideo(req.body.directory + '/' + req.body.editfile);
	let musicstring = `amix=inputs=2:duration=${req.body.duration}`;
	video.input(req.body.musicurl).complexFilter([
		musicstring
	]).saveToFile(req.body.directory + '/' + req.body.filename + '.mp4').
	on('end', () => {
		res.status(200).json({
			success : true,
			filename : req.body.filename + '.mp4'
		});
		console.log("Done");
	}).
	on("error", (err) => {
		console.log(err)
		res.status(200).json({
			success : false
		})
	});
})

app.listen(3000,() => {
	console.log("Listening....")
})