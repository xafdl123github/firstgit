        $(function(){
            var screenx=$(window).width();
            var screeny=$(window).height();
            var DialogDiv_width=$(".DialogDiv").width();
            var DialogDiv_height=$(".DialogDiv").height();
            $(".DialogDiv").css("left",(screenx/2-DialogDiv_width/2)+"px");
            $(".DialogDiv").css("top",(screeny/2-DialogDiv_height/2)+"px");
            $("body").css("overflow","hidden");
        });




