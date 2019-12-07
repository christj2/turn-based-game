var ajaxData='';
var triggeredUpdate = false;
var personHtml = '<div class="personHtml animate"></div>';
var person2Html = '<div class="person2Html animate"></div>';
var spot_html = '<div class="spot spotHover"></div>';
var currentCursor = 'default';
var animateCellArray = null;
var animateInterval = 1000/60;//60 times per second

function update(){
    if(triggeredUpdate==true){
        return;
    }
    //console.log('update');
    $.ajax({
        url: 'api/game',
        method: "POST",
        data: ajaxData,
        //async: false,
        'success': function (data) {
            data = JSON.parse(data);
            var message = data['message'];

            if( message == "create" ){
                $('#create').animate({'opacity':'1'},'slow').css('pointer-events','all');
                return;
            }else if( message == 'start' ){
                window.location.reload();
                $('#create').hide();
                $("#game-board").show('slow');
                updateBoard(data['game']);
            }else if( message == 'continue' ){
                $('#create').hide();
                $("#game-board").show('slow');
                updateBoard(data['game']);
            }else if( message == 'createPlayer2' ){
                $('#create').animate({'opacity':'1'},'slow').css('pointer-events','all');
                $('#newGameSubmit').addClass('player2')
            }else if(
                message == 'update' ||
                message == 'turnEnded' ||
                message == 'upgradePerson'
            ){
                updateBoard(data['game']);
            }else if( message == 'selectPerson' ){
                currentCursor = 'buyPerson1';
                updateBoard(data['game']);
            }else if( message == 'movePerson' ){
                console.log('move person message reecieved 2');
                currentCursor = 'default';
                updateBoard(data['game']);
            }else if( message == 'createdPlayer2' ){
                window.location.reload();
            }else if( message == 'wait' ){
                //console.log('waiting');
                //maybe add message to user
                triggeredUpdate = false;
                return;
            }else if( message == 'createdPlayer2' ){
                $('#create').hide();
                $("#game-board").show('slow');
                updateBoard(data['game']);
            }
            ajaxData = {'waitForUpdate' : data['game']['updated']};
            update();
            triggeredUpdate = false;
        }
    });
    ajaxData='';
}

update();

function updateBoard(game){
    $('#player1name').html(game['player1name']);
    $('#player1income').html('+ '+game['player1income']);
    $('#player1expenses').html('- '+game['player1expenses']);
    $('#player1revenue').html((game['player1revenue']<=0?"":"+") + game['player1revenue']);
    $('#player1money').html('$'+game['player1money']);
    $('#player2name').html(game['player2name']);
    $('#player2income').html('+ '+game['player2income']);
    $('#player2expenses').html('- '+game['player2expenses']);
    $('#player2revenue').html((game['player2revenue']<=0?"":"+") + game['player2revenue']);
    $('#player2money').html('$'+game['player2money']);
    $('#castle1').css('background-color',game['player1color']);
    $('#castle2').css('background-color',game['player2color']);
    if( game['currentPlayer'] == '1' ){
        $('.player2actions').css('visibility','hidden');
        $('#game-board').attr('data-player','1');
        if( game['turn'] == '1' ){
            $('.player1actions').css('visibility','hidden');
        }
        $('#player1turn').html("Your Turn");
        $('#player2turn').html("Waiting");
    }else if( game['currentPlayer'] == '2' ){
        $('.player1actions').css('visibility','hidden');
        $('#game-board').attr('data-player','2');
        if( game['turn'] == '0' ){
            $('.player2actions').css('visibility','hidden');
        }
        $('#player2turn').html("Your Turn");
        $('#player1turn').html("Waiting");
    }
    //console.log('update board', game['turn'],game['currentPlayer'])
    if( game['turn'] == 0 && game['currentPlayer'] == '1' ){
        $('.player1actions').css('visibility','');
    }else if( game['turn'] == 1 && game['currentPlayer'] == '2' ){
        $('.player2actions').css('visibility','');
    }

    //console.log(game)
    var board = JSON.parse(game['board']);
    var selectedCell = game['selectedCell'];
    if( selectedCell != 'none' ){
        selectedCell = JSON.parse(selectedCell);
    }
    //console.log(board);
    var createAnimateArray = false;
    if( animateCellArray == null ){
        createAnimateArray = true;
        animateCellArray = [];
    }
    var spots_html = '';
    for(var row=0;row<board.length;row++){
        if( createAnimateArray == true){
            animateCellArray[row] = [];
        }
        for(var col=0;col<board[row].length;col++){
            var tempHtml = spot_html;
            tempHtml = $(tempHtml).attr('data-row',row);
            tempHtml = $(tempHtml).attr('data-col',col);
            if(board[row][col] == "blank"){
                tempHtml = $(tempHtml).addClass('blank');
            }else{
                if( createAnimateArray == true){
                    animateCellArray[row][col] = 'null';
                }
                var player = board[row][col].charAt(0);
                var spotModifier = 'null';
                if(board[row][col].length > 0){
                    spotModifier = board[row][col].charAt(1);
                }
                var opacity = .6;
                if(selectedCell!='none' && selectedCell.row==row && selectedCell.col==col){
                    opacity = .2;
                }
                if(player == '1'){
                    tempHtml = $(tempHtml).css({
                        "background-color" : hexToRgbA(game['player1color'], opacity)
                    }).addClass('player1owned');
                }else if(player == '2'){
                    tempHtml = $(tempHtml).css({
                        "background-color" : hexToRgbA(game['player2color'], opacity)
                    }).addClass('player2owned');
                }else{
                    tempHtml = $(tempHtml).addClass('empty')
                    // .css({
                    //     "background-color" : hexToRgbA('#000', opacity)
                    // });
                }
                // if( animateCellArray[row][col] !== 'null' &&  != game['currentPlayer'] ) ){
                //     animateCellArray[row][col] = 'null';
                // }
                var spotModifierHtml = '';
                if( spotModifier=="1" ){
                    spotModifierHtml = $(personHtml).css({
                        'margin': '45% auto'
                    });
                    if(
                        animateCellArray[row][col] == "null" &&
                        board[row][col].indexOf("moved") == -1 &&
                        player == game['turn']+1 //&& //your player
                        //player == $('#game-board').data('player') && //your player
                        //(game['turn']+1 == parseInt(game['currentPlayer'])) //your turn
                    ){
                        animateCellArray[row][col] = {
                            'up':Math.round(Math.random()),
                            'position':Math.round(Math.random()*45),
                            'pixels':1
                        }
                    }
                }else if( spotModifier=="2" ){
                    console.log('spot modifier 2')
                    spotModifierHtml = $(person2Html).css({
                        'margin': '45% auto'
                    });
                    if(
                        animateCellArray[row][col] == "null" &&
                        board[row][col].indexOf("moved") == -1 &&
                        player == game['turn']+1 //&& //your player
                        //player == $('#game-board').data('player') && //your player
                        //(game['turn']+1 == parseInt(game['currentPlayer'])) //your turn
                    ){
                        animateCellArray[row][col] = {
                            'up':Math.round(Math.random()),
                            'position':Math.round(Math.random()*45),
                            'pixels':1
                        }
                    }
                }else if( animateCellArray[row][col] !== 'null' ){
                    animateCellArray[row][col] = 'null';
                }
            }
            tempHtml = $(tempHtml).html(spotModifierHtml);
            spots_html += tempHtml[0].outerHTML;
            spotModifierHtml = '';
        }
    }
    if( createAnimateArray == true){
        createAnimateArray = false;
        kickOffAnimator();
        // console.log('animage cell array created:', animateCellArray);
    }
    var player = $('#game-board').data('player');
    // console.log('update end');
    // console.log(player);
    // console.log($('#player'+player+'buyPerson').hasClass('cancel'));
    if( $('#player'+player+'buyPerson').hasClass('cancel') ){
        var temp_html = $("<div></div>");
        temp_html = $(temp_html).html(spots_html);
        // console.log('1', temp_html);
        // console.log('4', $(temp_html).find(".spot[data-col!='0']"));
        // console.log('5', $(temp_html).find(".spot[data-col!='0']").not('.blank'));
        $(temp_html).find(".spot[data-col!='0']").not('.blank').addClass("disabledSpot");
        // console.log('2', temp_html);
        spots_html = $(temp_html).eq(0).html();
        // spots_html = $(temp_html).outerHTML;
        // console.log('3', spots_html);
    }
    // console.log('spotshtml', spots_html)
    $('#spots').html(spots_html);
    // $("#player1money").html('$'+game['player1money']);
    // $("#player2money").html('$'+game['player2money']);
    // $("#player1turnName").html(game['player1name']);
    // $("#player2turnName").html(game['player2name']);
    $(".playerTurn").css('visibility','hidden');
    //console.log('turn decide:', typeof game.turn);
    if( game.turn == 0 ){
        $('#player1turn').css('visibility','visible');
    }else if( game.turn == 1 ){
        $('#player2turn').css('visibility','visible');
    }
    changeCursor();
}

function isUndefined(arr, index1, index2) {
    try{
        return arr[index1][index2] == undefined;
    }catch(e){
        return true;
    }
}

function kickOffAnimator(){
    setInterval(function(){
        animatePeople();
    },animateInterval);
}

function animatePeople(){
    //console.log('animate people')
    //console.log('animate people 2')
    for(var row=0;row<=animateCellArray.length;row++){
        for(var col=0;col<=animateCellArray[0].length;col++){
            if(!isUndefined(animateCellArray,row,col)&&animateCellArray[row][col]!=='null'){
                //console.log('animate peops', animateCellArray);
                //console.log('animate peops row', row, animateCellArray[row]);
                //console.log('animate peops col', col,  animateCellArray[row][col]['position']);
                //console.log($('.spot[data-row="' + row + '"][data-col="'+col+'"]'))
                $('.spot[data-row="' + row + '"][data-col="'+col+'"]').find('.animate').css("margin-top",animateCellArray[row][col]['position']+'%');
                if(animateCellArray[row][col]['up']==1&&animateCellArray[row][col]['position']<=5){
                    animateCellArray[row][col]['up']=0;
                    animateCellArray[row][col]['position'] = animateCellArray[row][col]['position']-animateCellArray[row][col]['pixels'];
                }else if(animateCellArray[row][col]['up']==1){
                    animateCellArray[row][col]['position'] = animateCellArray[row][col]['position']-animateCellArray[row][col]['pixels'];
                }else if(animateCellArray[row][col]['up']==0&&animateCellArray[row][col]['position']>=45){
                    animateCellArray[row][col]['up']=1;
                    animateCellArray[row][col]['position'] = animateCellArray[row][col]['position']+animateCellArray[row][col]['pixels'];
                }else if(animateCellArray[row][col]['up']==0){
                    animateCellArray[row][col]['position'] = animateCellArray[row][col]['position']+animateCellArray[row][col]['pixels'];
                }
            }
        }
    }
}

function changeCursor(){
    if( currentCursor == 'buyPerson1' ){
        var newPerson = $(personHtml).css('display','none');//.appendTo("body");
        $('body').addClass('noCursor').append(newPerson);
        $('.spot').addClass('noCursor').removeClass('spotHover');
        $('.playerAction').addClass('noCursor');
        $(document).one('mouseup', function(e){
            if( currentCursor == 'buyPerson1' ){
                $(newPerson).css({
                   left:  e.pageX,//-15,
                   top:   e.pageY,//+3,
                   'pointer-events': 'none',
                   cursor: 'none',
                   'display':'block'
               }).addClass('currentCursor');
            }
        });
    }else if( currentCursor == 'default' ){
        $('.currentCursor').remove();
        $('body').removeClass('noCursor');
        $('.spot').removeClass('noCursor').addClass('spotHover');
        $('.playerAction').removeClass('noCursor');
    }
}

$(document).ready(function(){
    $('#newGameSubmit').click(function(){
        ajaxData = {'newGame':{
                'player1name':$('#p1n').val(),
                'player1color':$('#p1c').val(),
                //'board': "yayboard2"
            }
        };
        if( $(this).hasClass('player2') ){
            ajaxData = {'player2join':{
                    'player2name':$('#p1n').val(),
                    'player2color':$('#p1c').val(),
                    //'board': "yayboard2"
                }
            };
        }
        update();
    });
    $('#spots').on('mousedown','.spot',function(){
        var player = $('#game-board').data('player');
        if( $('#player'+player+'buyPerson').hasClass('cancel') ){
            ajaxData = {'buyPerson1':{
                    'row':$(this).data('row'),
                    'column':$(this).data('col')
                }
            };
            $('.playerAction.cancel').trigger('mousedown');
        }else{
            ajaxData = {'spotClick':{
                    'row':$(this).data('row'),
                    'column':$(this).data('col')
                }
            };
        }
        update();
        triggeredUpdate = true;
    });
    $('#spots').on('mouseenter','.spot',function(){
        if( currentCursor == 'buyPerson1' ){
            var player = $('#game-board').data('player');
            if( (player == '1' && $(this).hasClass('player1owned')) || (player == '2' && $(this).hasClass('player2owned')) ){
                $(this).css('opacity','.8');
            }
        }
    });
    $('#spots').on('mouseleave','.spot',function(){
        if( currentCursor == 'buyPerson1' ){
            $(this).css('opacity','');
        }
    });
    $('#player1buyPerson, #player2buyPerson').on('mousedown',function(){
        if( $(this).hasClass('cancel') ){
        //    currentCursor = 'default';
            $(this).removeClass('cancel').html('Buy Person ($20)');
            $(".spot").removeClass("disabledSpot");
        }else{
            $(this).addClass('cancel').html('Cancel');
            $(".spot[data-col!='0']").not('.blank').addClass("disabledSpot");
        }
        //    currentCursor = 'buyPerson1';
        //changeCursor();
    });
    $('#player1endTurn, #player2endTurn').on('mousedown',function(){
        // console.log('en turn')
        // for(var row2=0;row2<=animateCellArray.length;row2++){
        //     for(var col2=0;col2<=animateCellArray[0].length;col2++){
        //         console.log('set null');
        //         animateCellArray[row2][col2] = "null";
        //     }
        // }
        // console.log('ater all')
        ajaxData = {'endTurn':$('#game-board').data('player')};
        update();
        triggeredUpdate = true;
    });
    $(document).on('mousemove', function(e){
        if( currentCursor == 'buyPerson1' ){
            $('.currentCursor').css({
               left:  e.pageX,//-15,
               top:   e.pageY,//+3,
               'pointer-events': 'none',
               cursor: 'none'
            });
        }
    });
});

function hexToRgbA(hex,opacity){
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
    }
    throw new Error('Bad Hex');
}
