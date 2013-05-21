var chatClient = {
	host : '127.0.0.1',
	port : 8991,
	socket : null,
    uinfo: {},
    renum: 2,
    hb : 0,
	cb: {
		connect: function(success, data){
			console.log("server connect, success:"+success+" data:"+data);
		},
		send: function(data){
			console.log("send data"+data)
		},
		receive: function(data){
			console.log("receive data"+data)
		},
		close: function(){
            this.connected = 0;
			console.log("server close");
		}
	},
	connected: 0,
	init : function(host, port, target, swf) {
		this.host = host;
		this.port = port;
		this.socket = new jSocket(this.ready, this.connect,
		this.receive, this.close);
		this.socket.setup(target, swf);
		
	},
	ready: function() {
		chatClient.socket.connect(chatClient.host, chatClient.port);
	},

    retry: function() {
        if(this.renum < 1) {
            return 0;
        }
        this.ready();
        this.renum--;
        return 1;
    },

	connect: function(success, data) {
		return chatClient.cb.connect.call(this, success, data);
	},

	send: function(data) {
		this.socket.write(data);
		return this.cb.send.call(this, data);
	},

	receive: function(content) {
		return chatClient.cb.receive.call(this, content);
	},

	close: function() {
		return chatClient.cb.close.call(this);
	},

    selfClose: function() {
        chatClient.socket.close();
        $("#sendBtn").attr('disabled', 'disabled');
        chatClient.connected = 0;
        chatClient.clearhb();
    },

    heartbeat: function(seconds) {
        chatClient.hb = window.setInterval(function(){chatClient.send(JSON.stringify([7, {}]));}, seconds * 1000);
    },

    clearhb: function() {
        if(chatClient.hb) {
            window.clearInterval(chatClient.hb);
            chatClient.hb = 0;
        }
    }
};

function hiddenChat(_obj, hidden) {
    if(typeof(_obj) == 'string') {
        obj = document.getElementById(_obj);
    } else {
        obj = _obj;
    }
    hidden = hidden || false;
    if (hidden || obj.value == '隐') {
        $("#chat").css("top", 670);
        $("#chat").height('50');
        $("#chat_content").height('25');
        obj.value = '开';
    } else {
        $("#chat").css("top", 520);
        $("#chat").height('200');
        $("#chat_content").height('175');
        obj.value = '隐';
    }
    var obj = document.getElementById('chat_content');
    obj.scrollTop = obj.scrollHeight;
}

function checkScroll() {
    var obj = document.getElementById('chat_content');
    obj.scrollTop= obj.scrollHeight;
}

function sendMsg() {
    if(!chatClient.connected){
        alert('服务器没有连接');
        return;
    }

    var msgContent  = $.trim($("#msgContent").val());
    if(msgContent == "") {
        alert('请输入聊天内容');
        $("#msgContent").focus();
        return;
    }

    chatClient.send(JSON.stringify([6, {
        uid:chatClient.uinfo.uid,
        token: YL_SNS_CLIENT._config.token,
        uname:chatClient.uinfo.username,
        msg:msgContent
    }]));
    $("#msgContent").val('');
}

chatClient.cb.connect = function(success, data) {
    if(!success) {
        console.log("error:"+data);
        $("#chat_content").text('服务器连接失败');
        if(chatClient.retry()) {
            $("#chat_content").text('重连中');
        } else {
            hiddenChat("hiddenBtn", true);
        }
        return;
    }
    $("#chat_content").text('服务器连接成功');
    $("#chat_content").text('正在初始化用户信息');
    chatClient.socket.write(JSON.stringify([1, {
        uid: YL_SNS_CLIENT._config.uid,
        token: YL_SNS_CLIENT._config.token
    }]));
}

chatClient.cb.close = function() {
    $("#chat_content").text('聊天服务器关闭中.');
    hiddenChat("hiddenBtn", true);
    $("#sendBtn").attr('disabled', 'disabled');
    chatClient.connected = 0;
    chatClient.clearhb();
}

chatClient.cb.receive = function (data) {
    console.log('server:'+data);
    data = JSON.parse(data);
    switch (data[0]) {
        case -1:
            console.log("server error code:"+data[1]['code']+" msg:"+data[1]['msg']);
            break;
        case 2:     //
            $("#chat_content").html('<p>欢迎ZCHAT</p>');
            chatClient.uinfo = data[1];
            chatClient.connected = 1;
            $("#sendBtn").removeAttr('disabled');
            chatClient.heartbeat(45);
            break;
        case 3:
            $("#chat_content").text('初始化信息失败');
            chatClient.socket.close();
            $("#sendBtn").attr('disabled', 'disabled');
            chatClient.connected = 0;
            break;
        case 4:   //重新登录
            chatClient.socket.write(JSON.stringify([1, {
                uid: YL_SNS_CLIENT._config.uid,
                token: YL_SNS_CLIENT._config.token
            }]));
            break;
        case 5:
            $("#chat_content").text('帐号已在其它地方登录');
            chatClient.selfClose();
            break;
        case 6:
            $("#chat_content").append("<p>"+data[1]['uname']+"说："+data[1]['msg']+"</p>");
            checkScroll();
            break;
        case 7:
            console.log('hb upgrade success');
            break;
        case 8:
            $("#chat_content").text('长时间没活动，断开链接！');
            chatClient.selfClose();
            break;
        case 88:
            $("#chat_content").append("<p>"+data[1]['username']+" "+data[1]['star']+"星 通过了关卡："+data[1]['sid']+"</p>");
            checkScroll();
            break;
    }
}

jQuery(document).keypress(function(e){
    if(e.ctrlKey && e.which == 13 || e.which == 10) {
        jQuery("#sendBtn").click();
    } else if (e.shiftKey && e.which==13 || e.which == 10) {
        jQuery("#sendBtn").click();
    }
});
