var http=require("http");
var url = require("url");

function start(route, handle){
    function onRequest(req,res){
        var ontvangenData = "";
        var pathname= url.parse(req.url).pathname;

        req.setEncoding("utf8");
        //res.writeHead(200, {'Content-Type': 'text/plain; charset=utf8'});
        req.addListener("data", function(postDataChunk){
          ontvangenData += postDataChunk;
        });
        req.addListener("end", function(){
          route(pathname, handle, res, ontvangenData);
        });
    }

    http.createServer(onRequest).listen(9876);
    console.log("Server opgestart");
}

exports.start = start;
