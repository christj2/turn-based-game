$.ajax({
    url: 'api/browse',
    method: "GET",
    //data: ajaxData,
    //async: false,
    'success': function (data) {
        data = JSON.parse(data);
        $.each(data,function(e,i){
            $('.browse').append("<div class='game_button' data-id='"+i.id+"'><p class='game_button_text'>"+i.id+", "+i.player1name+"</p></div>");
        });
        //$('.browse').html(data);

        $(".game_button").click(function(){
            console.log('hello')
            var id = $(this).data("id");
            $.ajax({
                url: 'api/joinGame',
                method: "POST",
                data: {'id':id},
                //async: false,
                'success': function (data) {
                    console.log(data);
                    if(data == "joined"){
                        window.location = BASE_URL
                    }
                }
            });
        });
    }
});
