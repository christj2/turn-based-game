$(document).ready(function () {

    $('#new_game').click(function(){
        $.ajax({
            url: 'api/newGame',
            method: "GET",
            'success': function (data) {
                window.location = BASE_URL
            }
        });
    });


    $('#browse').click(function(){
        window.location = 'browse';
    });


    $('#menuButton').click(function(){
        if( $('#sideMenu').css('opacity') == '0' ){
            $('#sideMenu').animate({'opacity':'1'},'fast').css('pointer-events','all');
        }else{
            $('#sideMenu').animate({'opacity':'0'},'fast').css('pointer-events','none');
        }
    });

    $("#backgroundSlider").on("input",function(){
        var sat = $('#saturationSlider').val();
        var color = "hsl("+$(this).val()+","+sat+"%,80%)";
        $('.main').css('background-color',color);
    });

    $("#saturationSlider").on("input",function(){
        var hue = $('#backgroundSlider').val();
        var color = "hsl("+hue+","+$(this).val()+"%,80%)";
        $('.main').css('background-color',color);
    });

    $("#saturationSlider").val(50);
    $("#backgroundSlider").val(Math.floor(Math.random() * 360));
    $("#saturationSlider").trigger('input');
});

function perc2color(perc) {
	var r, g, b = 0;
	if(perc < 50) {
		r = 255;
		g = Math.round(5.1 * perc);
	}else {
		g = 255;
		r = Math.round(510 - 5.10 * perc);
	}
    b =  Math.round(2.55 * perc);
    return "rgba("+r+","+g+","+b+",.2)";
	// var h = r * 0x10000 + g * 0x100 + b * 0x1;
	// return '#' + ('000000' + h.toString(16)).slice(-6);
}
