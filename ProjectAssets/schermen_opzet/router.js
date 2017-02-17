function route(pathname, handle, res, ontvangenData){
    console.log("Ontvangen route: " + pathname);
    if (typeof handle[pathname] === 'function'){
        return handle[pathname](res, ontvangenData);
    } else {
        console.log("No handler for " + pathname);
        res.writeHead(404, {"Content-Type":"text/plan"});
        res.write("404 Not Found pathname: " + pathname);
        res.end();
    }
}

exports.route = route;
