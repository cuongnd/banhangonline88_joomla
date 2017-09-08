// Author: Sergio Castaño Arteaga
// Email: sergio.castano.arteaga@gmail.com

// ***************************************************************************
// General
// ***************************************************************************
//

var conf = {
    port: 8888,
    debug: false,
    dbPort: 6379,
    dbHost: '127.0.0.1',
    dbOptions: {},
    mainroom: 'MainRoom'
};

// External dependencies
var express = require('express'),
    http = require('http'),
    events = require('events'),
    request = require('request'),
    _ = require('underscore'),
    sanitize = require('validator').sanitize;
// HTTP Server configuration & launch
var app = express(),
    server = http.createServer(app);
server.listen(conf.port);

// Express app configuration
app.configure(function () {
    app.use(express.bodyParser());
    app.use(express.static(__dirname + '/static'));
});

var io = require('socket.io')(server);
var redis = require('socket.io-redis');
io.adapter(redis({host: conf.dbHost, port: conf.dbPort}));

var db = require('redis').createClient(conf.dbPort, conf.dbHost);

// Logger configuration
var logger = new events.EventEmitter();
logger.on('newEvent', function (event, data) {
    // Console log
    console.log('%s: %s', event, JSON.stringify(data));
    // Persistent log storage too?
    // TODO
});

// ***************************************************************************
// Express routes helpers
// ***************************************************************************

// Only authenticated users should be able to use protected methods
var requireAuthentication = function (req, res, next) {
    // TODO
    next();
};

// Send a message to all active rooms
var sendBroadcast = function (text) {
    _.each(io.nsps['/'].adapter.rooms, function (sockets, room) {
        var message = {'room': room, 'userName': '',name:'ServerBot', 'msg': text, 'date': new Date()};
        io.to(room).emit('newMessage', message);
    });
    logger.emit('newEvent', 'newBroadcastMessage', {'msg': text});
};

// ***************************************************************************
// Express routes
// ***************************************************************************

// Welcome message
app.get('/', function (req, res) {
    res.send(200, "Welcome to chat server");
});

// Broadcast message to all connected users
app.post('/api/broadcast/', requireAuthentication, function (req, res) {
    sendBroadcast(req.body.msg);
    res.send(201, "Message sent to all rooms");
});

// ***************************************************************************
// Socket.io events
// ***************************************************************************
var clients = {};
var messengerHistory={};
var listRoomUsingSameToken={};
var listRoom={};
io.sockets.on('connection', function (socket) {
    var ip= socket.request.socket.remoteAddress;
    var addedUser = false;
    // Welcome message on connection

    logger.emit('newEvent', 'userConnected', {'socket': socket.id});
    var name=socket.handshake.query['name'];
    var system_user_id=socket.handshake.query['system_user_id'];
    var userName=socket.handshake.query['userName'];
    var title=socket.handshake.query['title'];
    var url=socket.handshake.query['url'];
    var old_socket_id=socket.handshake.query['old_socket_id'];
    var os=socket.handshake.query['os'];
    var token=socket.handshake.query['token'];
    socket.name=name;
    socket.system_user_id=system_user_id;
    socket.userName=userName;
    socket.old_socket_id=old_socket_id;
    socket.ip=ip;
    socket.token=token;
    if(typeof listRoomUsingSameToken[socket.token]=="undefined"){
        listRoomUsingSameToken[socket.token]=[];
    }

    // Store user data in db
    db.hset([socket.id, 'connectionDate', new Date()], redis.print);
    db.hset([socket.id, 'socketId', socket.id], redis.print);
    db.hset([socket.id, 'userName', userName], redis.print);
    db.hset([socket.id, 'system_user_id', system_user_id], redis.print);
    db.hset([socket.id, 'name', name], redis.print);
    db.hset([socket.id, 'old_socket_id', old_socket_id], redis.print);
    db.hset([socket.id, 'os', os], redis.print);
    db.hset([socket.id, 'ip', ip], redis.print);
    db.hset([socket.id, 'token', token], redis.print);
    // echo globally (all clients) that a person has connected
    var current_user={
        id:system_user_id,
        name:name,
        userName:userName,
        socketId:socket.id,
        token:socket.token,
        old_socket_id:old_socket_id,
        system_user_id:system_user_id,
        ip:ip
    };
    socket.emit('connected', {
        socketId:socket.id
    });
    clients[socket.id]=current_user;
    update_user_online(socket,clients,io);
    // Join user to 'MainRoom'
    if(old_socket_id!=0){
        for (var room in listRoom){
            if (listRoom.hasOwnProperty(room)) {
                var listSocketId=listRoom[room];
                var index_of_socket_id = listSocketId.indexOf(old_socket_id);
                if (index_of_socket_id > -1) {
                    listRoom[room].splice(index_of_socket_id, 1);
                    listRoom[room].push(socket.id);
                    socket.join(room);
                    break;
                }
            }
        }
    }

    request('http://ipinfo.io', function(error, res, body) {
        console.log(JSON.parse(body))
    });

    for (var room in listRoom){
        var listSocket = io.sockets.sockets;
        listSocket.forEach(function(itemSocket) {
            if(userName==itemSocket.userName){
                itemSocket.join(room);

            }
        });
    }
    socket.join(conf.mainroom);
    logger.emit('newEvent', 'userJoinsRoom', {'socket': socket.id, 'room': conf.mainroom});
    // Confirm subscription to user
    socket.emit('subscriptionConfirmed', {'room': conf.mainroom});
    //update name from client


    // Notify subscription to all users in room
    var data = {'room': conf.mainroom,name:socket.name, 'userName': socket.userName, 'msg': '----- Joined the room -----','socketId':socket.id, 'id': null};
    io.to(conf.mainroom).emit('userJoinsRoom', data);
    if(url!=null && url!=""){
        var msg='<a href="'+url+'">'+title+'</a>';
    }
    // Notify subscription to all users in room
    var data = {'room': conf.mainroom,name:socket.name, 'userName': socket.userName, 'msg': 'viewing page:'+msg,url:url,'socketId':socket.id, 'id': null};
    io.to(conf.mainroom).emit('sendDocumentPage', data);

    var listMessenger=messengerHistory[conf.mainroom];
    if(typeof  listMessenger=="undefined")
    {
        listMessenger=[];
    }
    data={
        listMessenger:listMessenger
    };
    logger.emit('newEvent', 'getListMessenger', data);
    socket.emit('getListMessenger', data);

    // User wants to subscribe to [data.rooms,data.client_socket_id,data.clientUserName]
    socket.on('subscribe', function (data) {
        // Get user info from db
        var client_socket_id=data.client_socket_id;
        var clientUserName=data.clientUserName;
        var list = io.sockets.sockets;
        var client_socket=null;
        console.log("Connected sockets:");
        list.forEach(function(s) {
            if(client_socket_id==s.id){
                client_socket=s;
            }
        });
        db.hget([socket.id, 'name'], function (err, name) {

            // Subscribe user to chosen rooms
            _.each(data.rooms, function (room) {
                room = room.replace(" ", "");
                socket.join(room);
                if(typeof listRoom[room]=="undefined"){
                    listRoom[room]=[];
                }
                listRoom[room].push(socket.id);

                var index_of_room = listRoomUsingSameToken[socket.token].indexOf(room);
                if (index_of_room == -1) {
                    listRoomUsingSameToken[socket.token].push(room);
                }
                updateJoinRoomSocketUseTogetherUserName(clientUserName,room);
                if(client_socket!=null)
                {
                    client_socket.join(room);
                    if(typeof listRoom[room]=="undefined"){
                        listRoom[room]=[];
                    }
                    listRoom[room].push(client_socket.id);
                }
                logger.emit('newEvent', 'userJoinsRoom', {'socket': socket.id,name:name, 'userName': socket.userName, 'room': room});

                // Confirm subscription to user
                socket.emit('subscriptionConfirmed',
                    {
                        'room': room,
                        'client_socket_id': data.client_socket_id,
                        'clientUserName': data.clientUserName,
                        'name': data.name,
                        'userName': data.userName,
                    }
                );



                // Notify subscription to all users in room
                var message = {
                    'room': room,
                    'userName': userName,
                    'name': name,
                    'msg': '----- Joined the room -----',
                    'socketId': socket.id
                };
                io.to(room).emit('userJoinsRoom', message);
            });
        });
    });

    // User wants to unsubscribe from [data.rooms]
    socket.on('unsubscribe', function (data) {
        // Get user info from db
        logger.emit('newEvent', 'unsubscribe',data);
        db.hget([socket.id, 'name'], function (err, name) {

            // Unsubscribe user from chosen rooms
            _.each(data.rooms, function (room) {
                if (room != conf.mainroom) {
                    socket.leave(room);
                    var index_of_room = listRoomUsingSameToken[socket.token].indexOf(room);
                    if (index_of_room > -1) {
                        listRoomUsingSameToken[socket.token].splice(index_of_room, 1);
                    }
                    logger.emit('newEvent', 'userLeavesRoom', {
                        'socket': socket.id,
                        'name': name,
                        'userName': socket.userName,
                        'room': room
                    });

                    // Confirm unsubscription to user
                    socket.emit('unsubscriptionConfirmed', {'room': room});

                    // Notify unsubscription to all users in room
                    var message = {
                        'room': room,
                        'name': name,
                        'userName': socket.userName,
                        'msg': '----- Left the room -----',
                        'id': null
                    };
                    io.to(room).emit('userLeavesRoom', message);
                }
            });
        });
    });

    // User wants to know what rooms he has joined
    socket.on('getRooms', function (data) {
        socket.emit('roomsReceived', socket.rooms);
        logger.emit('newEvent', 'userGetsRooms', {'socket': socket.id});
    });

    // Get users in given room
    socket.on('getUsersInRoom', function (data) {
        var usersInRoom = [];
        var socketsInRoom = _.keys(io.nsps['/'].adapter.rooms[data.room]);
        for (var i = 0; i < socketsInRoom.length; i++) {
            db.hgetall(socketsInRoom[i], function (err, obj) {
                usersInRoom.push({'room': data.room, 'userName': obj.userName, 'id': null});
                // When we've finished with the last one, notify user
                if (usersInRoom.length == socketsInRoom.length) {
                    socket.emit('usersInRoom', {'users': usersInRoom});
                }
            });
        }
    });

    // User wants to change his nickname
    socket.on('setNickname', function (data) {
        if(ExistsNickName(data.name,socket.token)){
            console.log("ExistsNickName");
            socket.emit('existsNickName', {exists:1});
        }else{
            socket.name=data.name;
            // Get user info from db
            db.hget([socket.id, 'name'], function (err, userName) {
                // Store user data in db
                db.hset([socket.id, 'name', data.name], redis.print);
                console.log("thu hien doi ten tren server");
                var system_user_id=socket.system_user_id;
                var current_user={
                    name:data.name,
                    userName:socket.userName,
                    socketId:socket.id,
                    system_user_id:system_user_id,
                    ip:socket.ip,
                    token:socket.token,
                }
                clients[socket.id]=current_user;
                updateNameTOSocketUseTogetherToken(socket,data);
                update_user_online(socket,clients,io);
                // Notify all users who belong to the same rooms that this one
                _.each(socket.rooms, function (room) {
                    if (room) {
                        var info = {
                            'room': room,
                            'oldName': name,
                            'newName': data.name,
                            'id': null
                        };
                        logger.emit('newEvent', 'userSetsNickname', {
                            'socket': socket.id,
                            'oldName': name,
                            'newName': data.name,
                        });

                        io.to(room).emit('userNicknameUpdated', info);
                    }
                });
            });
        }


    });

    // New message sent to group
    socket.on('newMessage', function (data) {
        var listMessenger=messengerHistory[data.room];
        if(typeof listMessenger=="undefined"){
            listMessenger=[];
        }
        if(listMessenger.length>100){
            listMessenger = listMessenger.reverse();
            listMessenger.pop();
            listMessenger = listMessenger.reverse();
        }
        var msg_key=create_random_number_key(6);
        var msgItem=data;
        msgItem.msg_key=msg_key;
        msgItem.socketId=socket.id;
        msgItem.name=socket.name;
        msgItem.token=socket.token;
        msgItem.ip=socket.ip;
        msgItem.userName=socket.userName;
        msgItem.system_user_id=socket.system_user_id;
        logger.emit('newEvent',"msgItem", msgItem);
        listMessenger.push(msgItem);
        messengerHistory[data.room]=listMessenger;
        var client_socket_id=data.client_socket_id;

        db.hgetall(socket.id, function (err, obj) {
            if (err) return logger.emit('newEvent', 'error', err);
            // Check if user is subscribed to room before sending his message
            if (_.contains(_.values(socket.rooms), data.room)) {
                var message = {'socketId':socket.id,msg_key:msg_key,token:socket.token,'client_socket_id':client_socket_id,'room': data.room,system_user_id:socket.system_user_id, 'userName': socket.userName,name:socket.name, 'msg': data.msg, 'date': new Date()};
                // Send message to room
                io.to(data.room).emit('newMessage', message);
                logger.emit('newEvent', 'newMessage', message);
            }
        });
    });

    // getListMessenger
    socket.on('getListMessenger', function (data) {
        var listMessenger=messengerHistory[data.room];
        if(typeof  listMessenger=="undefined")
        {
            listMessenger=[];
        }
        console.log(listMessenger);
        io.to(data.room).emit('getListMessenger', listMessenger);
       /* logger.emit("getListMessenger", data);
        db.hgetall(socket.id, function (err, obj) {
            if (err) return logger.emit('newEvent', 'error', err);
            // Check if user is subscribed to room before sending his message
            if (_.contains(_.values(socket.rooms), data.room)) {

                var listMessenger = messengerHistory[data.room];
                // Send message to room
                io.to(data.room).emit('getListMessenger', listMessenger);
                logger.emit('getListMessenger', 'getListMessenger', listMessenger);
            }
        });*/
    });
    socket.on('getListUserOnline', function (data) {

        var listUserOnline={};
        for (var key in clients){
            var client=clients[key];
            var token=client.token;
            if (typeof listUserOnline[token] =="undefined") {
                listUserOnline[token]=client;
            }
        }
        socket.emit('getListUserOnline', listUserOnline);
        logger.emit('newEvent', 'getListUserOnline', listUserOnline);
    });
    socket.on('getListSupportUserOnline', function (data) {
        socket.emit('getListSupportUserOnline', clients);
        logger.emit('newEvent', 'getListSupportUserOnline', clients);
    });

    socket.on('getSocketId', function (data) {
        socket.emit('returnSocketId', {
            socketId:socket.id
        });
    });


    socket.on('getMyRoom', function (data) {
        var old_socket_id=socket.old_socket_id;
        var listMyRoom=[];
        if(old_socket_id!=0){
            for (var room in listRoom){
                if (listRoom.hasOwnProperty(room)) {
                    var listSocketId=listRoom[room];
                    var index_of_socket_id = listSocketId.indexOf(old_socket_id);
                    if (index_of_socket_id > -1) {
                        listMyRoom.push(room);
                        break;
                    }
                }
            }
        }


        socket.emit('responseRoom', {
            listMyRoom:listMyRoom
        });
    });

    // Clean up on disconnect
    socket.on('disconnect', function (client) {
        delete  clients[socket.id];
        update_user_online(socket,clients,io);
        // Get current rooms of user
        var rooms = socket.rooms;
        // Get user info from db
        db.hgetall(socket.id, function (err, obj) {
            if (err) return logger.emit('newEvent', 'error', err);
            logger.emit('newEvent', 'userDisconnected', {'socketId': socket.id,token:socket.token,name:socket.name, 'userName': socket.userName});

            // Notify all users who belong to the same rooms that this one
            _.each(rooms, function (room) {
                if (room) {
                    var message = {
                        'room': room,
                        'name': socket.name,
                        'userName': socket.userName,
                        'msg': '----- Left the room -----',
                        'id': null
                    };
                    io.to(room).emit('userLeavesRoom', message);
                }
            });
        });

        // Delete user from db
        db.del(socket.id, redis.print);
    });
});
function update_user_online(socket,clients,io){
    console.log('list user online');
    console.log(clients);
    var key_emit="update-list-user-online";

    var listUserOnline={};
    for (var key in clients){
        var client=clients[key];
        var token=client.token;
        if (typeof listUserOnline[token] =="undefined") {
            listUserOnline[token]=client;
        }
    }


    socket.broadcast.emit(key_emit,listUserOnline);
    io.sockets.emit(key_emit, listUserOnline);
}
// Automatic message generation (for testing purposes)
if (conf.debug) {
    setInterval(function () {
        var text = 'Testing rooms';
        sendBroadcast(text);
    }, 60000);
}
create_random_key = function (length) {
    if(typeof length==="undefined"){
        length=6;
    }
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
};

create_random_number_key = function (length) {
    if(typeof length==="undefined"){
        length=6;
    }
    var text = "";
    var possible = "0123456789";

    for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
};

updateNameTOSocketUseTogetherToken=function(socket,data){
    var listSocket = io.sockets.sockets;
    for (var socketId in clients){
        var current_user=clients[socketId];
        var token=current_user.token;
        if(socket.id!=current_user.socketId && socket.token==token){
            listSocket.forEach(function(itemSocket) {
                if(current_user.socketId==itemSocket.id){
                    var info = {
                        'oldName': socket.name,
                        'newName': data.name,
                    };
                    itemSocket.emit('userNicknameUpdatedLable', info);
                    itemSocket.name=data.name;
                }
            });
        }
    }
}
updateJoinRoomSocketUseTogetherUserName=function(clientUserName,room){
    var listSocket = io.sockets.sockets;
    listSocket.forEach(function(itemSocket) {
        if(itemSocket.userName==clientUserName ){
            itemSocket.join(room);

        }
    });
}
ExistsNickName=function(name,token){
    var listSocket = io.sockets.sockets;
    var flag_exists=false;
    listSocket.forEach(function(itemSocket) {
        if(itemSocket.token!=token && itemSocket.name==name ){
            flag_exists= true;
            console.log("itemSocket.token:"+itemSocket.token);
            console.log("token:"+token);
            console.log("itemSocket.name:"+itemSocket.name);
            console.log("name:"+name);

        }
    });
    return flag_exists;
}
