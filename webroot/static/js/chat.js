var users;
var socket = {
    config : {
        ip :"127.0.0.1",
        port :8991,
        flashcontainer :"flashcontainer",
        auto :true
    },
    connect : function() {
        socket.flash.log("begin connect to session server");
        sysinfo("[系统提示] 正在连接聊天室服务器...<br />");
        socket.flash.connect(socket.config.ip, socket.config.port);
    },
    send : function(msg) {
        if (socket.isConnected >= 1) {
            socket.flash.send(msg);
        }
    },
    loaded : function() {
        socket.flash = document.getElementById("socketBridge");
        socket.isConnected = 0;
        if (socket.config.auto) {
            socket.connect();
        }
    },
    connected : function() {
        socket.isConnected = 1;
        socket.flash.log("connected to session server");
        socket.flash.send('setname||' + uname);
        sysinfo("[系统提示] 已连接到服务器，正在初始化帐户...<br />");
    },
    close : function() {
        socket.flash.close();
        socket.isConnected = 0;
        socket.flash.log("close connection");
    },
    disconnected : function() {
        socket.isConnected = 0;
        socket.flash.log("disconnected");
    },
    ioError : function(msg) {
        socket.flash.log(msg);
        socket.isConnected = 0;
    },
    securityError : function(msg) {
        socket.flash.log(msg);
        socket.isConnected = 0;
    },
    receive : function(msg) {
        socket.flash.log("receive from server:" + msg);
        recvmsg(msg);
    }
};
/**
 * 初始化聊天室
 * 
 * @return
 */
function initChatClient(ip, port) {
    var so = new SWFObject("./static/js/socket_bridge.swf", "socketBridge",
            "1", "1", "9", "#ffffff");
    so.addParam("allowscriptaccess", "always");
    so.addVariable("scope", "socket");
    so.write(socket.config.flashcontainer);
    socket.config.ip = ip;
    socket.config.port = port;

}
/**
 * 插入屏幕
 * 
 * @param msg
 * @return
 */
function sysinfo(msg) {
    jQuery('#msglist').append(msg);
}
/**
 * 接收到消息的回调函数
 * 
 * @param msg
 * @return
 */
function recvmsg(msg) {
    msg = decodeURIComponent(msg);
    var txt = '';
    var now = new Date;
    var ntime = now.getHours() + ':' + now.getMinutes() + ':'
            + now.getSeconds();
    if ("loginsuccess" == msg) {
        sysinfo('[系统提示] 登录成功！<br />');
        socket.flash.send('getusers');
        sysinfo('[系统提示] 正在获取已登录用户信息...<br />');
    } else if ("userexists" == msg) {
        socket.close();
        sysinfo('[系统提示] 您已登录，请不要重复登陆！<br />');
    }else {
        var data = eval('(' + msg + ')');
        if('userlist' == data.type) {
            sysinfo('[系统提示] 获取成功！<br />');
            users = data.userlist;
            init_userlist();
        }else if('userloginout' == data.type) {
            sysinfo('[系统提示] '+data.name+'退出的聊天室<br />');
            $("#"+data.from).remove();
            var uid = data.from;
            delete(users[uid]);
            
        }else if('newuser' == data.type) {
            sysinfo('[系统提示] '+data.name+'来到了聊天室<br />');
            var uid = data.from;
            users[uid] = data.name;
            init_userlist();
        }else if (data.type == 'msg') {
            var ntime = now.getHours() + ':' + now.getMinutes() + ':'
                    + now.getSeconds();
            if (data.to == '0') {
                var txt = ntime + ': <span style="color:red">' + users[data.from]
                        + '</span> 对 <span style="color:red">所有人</span> 说 : '
                        + data.msg + '<br />';
            } else {
                var txt = ntime + '<span style="color:red">' + users[data.from]
                        + '</span> 对你说 : ' + data.msg
                        + '<br />';
            }
            sysinfo(txt);
        }
    }

}
/**
 * 初始化已有用户
 * 
 * @return
 */
function init_userlist() {
    var html = "<ul>";
    for(var k in users) {
        var name = users[k];
        if(users[k] == uname) {
            name = '你';
        }
        html += '<li id="'+k+'">'+name+'</li>';
    }
    html += '</ul>';
    $("#pleft").html(html);
}
function sendmsg() {
    var input = document.getElementById('msgcontent');
    var msg = input.value;
    socket.send(msg);
    sysinfo('你说: '+msg+"<br/>");
    input.value = '';
}
function HTMLEnCode(str) {
    var s = "";
    if (str.length == 0)
        return "";
    s = str.replace(/&/g, "&gt;");
    s = s.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    s = s.replace(/    /g, "&nbsp;");
    s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    s = s.replace(/\n/g, "<br>");
    return s;
}
