<?php
//echo 'hi';die;

function game(){
    //session_unset();die;
    //$_SESSION = array();
    //session_destroy();
    customSessionStart();
    //echo 'in';die;
    connect();
    //unset($_SESSION['game']);
    $return = array();

    if( !isset($_SESSION['game']) && !isset($_POST['newGame']) ){
        $return['message'] = 'create';
    }elseif( isset($_POST['newGame']) ){
        $game = $_POST['newGame'];
        //attempt to join game
        //$return['games'] = select("game", array("*"), array('full'=>0));


        //no games. so create
        $game['board'] = array();
        $row=0;
        $column=0;
        for($i=0;$i<=47;$i++){
            if($column == 0){
                $game['board'][] = array();
            }
            $game['board'][$row][$column] = 'null';
            $column++;
            if($column==8){
                $column = 0;
                $row++;
            }
        }

        $return['game'] = $game;
        $return['message'] = 'start';
        $game['board'] = json_encode($game['board']);
        $game['player2name'] = 'nothing';
        $game['player2color'] = 'nothing';
        $game['player1session'] = session_id();
        $game['player2session'] = NULL;
        $game['player1money'] = 0;
        $game['player1income'] = 0;
        $game['player1expenses'] = 0;
        $game['player1revenue'] = 0;
        $game['player2money'] = 0;
        $game['player2income'] = 0;
        $game['player2expenses'] = 0;
        $game['player2revenue'] = 0;
        $id = insert($game);
        $_SESSION['game'] = $id;
        //echo json_encode($id);die;

    }elseif( isset($_POST['spotClick']) ){
        $id = $_SESSION['game'];
        $_SESSION['game'] = $id;
        $game = select('game',array('*'),array('id'=>"$id"))[0];
        $turn = $game['turn'];
        $player1money = $game['player1money'];
        $player2money = $game['player2money'];

        $player = "";
        if(session_id()==$game['player1session'] && $game['turn'] == 0 ){
            $player = "1";
            $otherPlayer = "2";
            $nextTurn = 1;
        }elseif(session_id()==$game['player2session'] && $game['turn'] == 1){
            $player = "2";
            $otherPlayer = "1";
            $nextTurn = 0;
        }else{
            echo json_encode(array('message'=>'wait'));
            die;
        }

        $board = json_decode($game['board']);
        $row = $_POST['spotClick']['row'];
        $col = $_POST['spotClick']['column'];
        $clickedCellValue = $board[$row][$col];
        $clickedCellVwP1 = $board[$row][$col] .'1';
        $clickedCellVwP2 = $board[$row][$col] .'2';

        $return['message'] = 'continue';


        $personCode = $player.'1';
        $person2Code = $player.'2';
        $selected = $game['selectedCell'];
        //var_dump($selected);die;
        if( $selected != "none" ){ //have selected person, so this is move

            $selectedCellArray = json_decode($game['selectedCell'],true);
            $selectedRow = $selectedCellArray['row'];
            $selectedCol = $selectedCellArray['col'];
            $selectedPersonLvl = $board[$selectedRow][$selectedCol][1];

            if( $selectedRow==$row && $selectedCol==$col ){ //clicked the currently selected space. unselect
                $selected = 'none';
            }elseif( //normal move
                $row + 1 >= $selectedRow &&
                $row - 1 <= $selectedRow &&
                $col + 1 >= $selectedCol &&
                $col - 1 <= $selectedCol &&
                $clickedCellValue != 'blank'
            ){
                if( //other players' normal space
                    (
                        (
                            $player=='2' && $clickedCellValue == '1' ||
                            $player=='1' && $clickedCellValue == '2'
                        ) &
                        $selectedPersonLvl == '1'
                    ) &&
                    (
                        ( isset($board[$row+1]) && isset($board[$row+1][$col]) && ($board[$row+1][$col]==$clickedCellVwP1||$board[$row+1][$col]==$clickedCellVwP2) )||
                        ( isset($board[$row]) && isset($board[$row][$col+1]) && ($board[$row][$col+1]==$clickedCellVwP1||$board[$row][$col+1]==$clickedCellVwP2) )||
                        ( isset($board[$row+1]) && isset($board[$row+1][$col+1]) && ($board[$row+1][$col+1]==$clickedCellVwP1||$board[$row+1][$col+1]==$clickedCellVwP2) )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col]) && ($board[$row-1][$col]==$clickedCellVwP1||$board[$row-1][$col]==$clickedCellVwP2) )||
                        ( isset($board[$row]) && isset($board[$row][$col-1]) && ($board[$row][$col-1]==$clickedCellVwP1||$board[$row][$col-1]==$clickedCellVwP2) )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col-1]) && ($board[$row-1][$col-1]==$clickedCellVwP1||$board[$row-1][$col-1]==$clickedCellVwP2) )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col+1]) && ($board[$row-1][$col+1]==$clickedCellVwP1||$board[$row-1][$col+1]==$clickedCellVwP2) )||
                        ( isset($board[$row+1]) && isset($board[$row+1][$col-1]) && ($board[$row+1][$col-1]==$clickedCellVwP1||$board[$row+1][$col-1]==$clickedCellVwP2) )
                    )
                ){
                    echo json_encode(array('message'=>'wait'));
                    die;
                }
                if( //moved onto own lvl 1. upgrade
                    isset($board[$row][$col][1]) &&
                    $board[$row][$col][0].$board[$row][$col][1] == $player.'1'
                ){
                    $return['message'] = 'upgradePerson';
                    $board[$row][$col] = $person2Code."moved";
                }elseif( //moved onto own lvl 2. die
                    isset($board[$row][$col][1]) &&
                    $board[$row][$col][0].$board[$row][$col][1] == $player.'2'
                ){
                    echo json_encode(array('message'=>'wait'));
                    die;
                }elseif( //lvl1 move onto any lvl 1. die.
                        $board[$row][$col][0] == $otherPlayer &&
                        isset($board[$row][$col][1]) &&
                        (
                            $board[$row][$col][1] == '1' ||
                            $board[$row][$col][1] == '2'
                        ) &&
                        $selectedPersonLvl == '1'
                ){
                    echo json_encode(array('message'=>'wait'));
                    die;
                }else{
                    $return['message'] = 'movePerson';
                    if( $selectedPersonLvl == '1' ){
                        $board[$row][$col] = $personCode."moved";
                    }elseif( $selectedPersonLvl == '2' ){
                        $board[$row][$col] = $person2Code."moved";
                    }
                }
                $selected = 'none';
                $board[$selectedRow][$selectedCol] = $player;
            }else{
                echo json_encode(array('message'=>'wait'));
                die;
            }
            //$turn = $nextTurn;
            // if( $player == "1" ){
            //     $player1money += findNumOwned("1",$board);
            // }elseif( $player == "2" ){
            //     $player2money += findNumOwned("2",$board);
            // }
        }elseif( //logic to select a person
            $board[$row][$col] == $personCode ||
            $board[$row][$col] == $person2Code
        ){
            //$board[$row][$col] = $personCode;
            $return['message'] = 'selectPerson';
            $selected = json_encode(array('row'=>$row,'col'=>$col));
        }else{
            echo json_encode(array('message'=>'wait'));
            die;
        }

        // //logic to add a spot if you click one next to one you own
        // if(
        //     $board[$row][$col] == "null"
        //     &&
        //     (
        //         ( isset($board[$row+1]) && isset($board[$row+1][$col]) && $board[$row+1][$col]==$player )||
        //         ( isset($board[$row]) && isset($board[$row][$col+1]) && $board[$row][$col+1]==$player )||
        //         ( isset($board[$row+1]) && isset($board[$row+1][$col+1]) && $board[$row+1][$col+1]==$player )||
        //         ( isset($board[$row-1]) && isset($board[$row-1][$col]) && $board[$row-1][$col]==$player )||
        //         ( isset($board[$row]) && isset($board[$row][$col-1]) && $board[$row][$col-1]==$player )||
        //         ( isset($board[$row-1]) && isset($board[$row-1][$col-1]) && $board[$row-1][$col-1]==$player )||
        //         ( isset($board[$row-1]) && isset($board[$row-1][$col+1]) && $board[$row-1][$col+1]==$player )||
        //         ( isset($board[$row+1]) && isset($board[$row+1][$col-1]) && $board[$row+1][$col-1]==$player )
        //     )
        // ){
        //     $board[$row][$col] = $player;
        //     $turn = $nextTurn;
        //     //add money
        //     if( $player == "1" ){
        //         $player1money += findNumOwned("1",$board);
        //     }elseif( $player == "2" ){
        //         $player2money += findNumOwned("2",$board);
        //     }
        // }

        $game['board'] = json_encode($board);
        $game['turn'] = $turn;
        $game['player1money'] = $player1money;
        $game['player2money'] = $player2money;
        $game['selectedCell'] = $selected;
        update(
            'game',
            array(
                'board'=>$game['board'],
                'turn'=>$turn,
                'player1money'=>$player1money,
                'player2money'=>$player2money,
                'selectedCell'=>$selected
            ),
            array('id'=>"$id")
        );
        $return['game'] = $game;

    }elseif( isset($_POST['buyPerson1']) ){
        $id = $_SESSION['game'];
        $_SESSION['game'] = $id;
        $game = select('game',array('*'),array('id'=>"$id"))[0];
        $turn = $game['turn'];
        $player1money = $game['player1money'];
        $player2money = $game['player2money'];

        $board = json_decode($game['board']);
        $row = $_POST['buyPerson1']['row'];
        $col = $_POST['buyPerson1']['column'];

        $player = "";
        if(
            session_id() == $game['player1session'] &&
            $game['turn'] == 0 &&
            $col == 0
        ){
            $player = "1";
        }elseif(
            session_id() == $game['player2session'] &&
            $game['turn'] == 1 &&
            $col == count($board[0])-1
        ){
            $player = "2";
        }else{
            echo json_encode(array('message'=>'wait'));
            die;
        }

        if(
            $board[$row][$col] == $player
            &&
            (
                ($player=="1" && $player1money>=20) ||
                ($player=="2" && $player2money>=20)
            )
            //&&
            //!playerHasPieceSelected($board,$player)()
        ){
            $board[$row][$col] = $player."1";
            //add money
            if( $player == "1" ){
                $player1money -= 20;
            }elseif( $player == "2" ){
                $player2money -= 20;
            }
        }
        $game['board'] = json_encode($board);
        $game['player1money'] = $player1money;
        $game['player2money'] = $player2money;
        update(
            'game',
            array(
                'board'=>$game['board'],
                'player1money'=>$player1money,
                'player2money'=>$player2money
            ),
            array('id'=>"$id")
        );

        $return['message'] = 'continue';
        $return['game'] = $game;

    }elseif( isset($_POST['player2join']) ){
        //todo add auth
        $post = $_POST['player2join'];
        $game = $_SESSION['game'];
        $return['game'] = select('game',array('*'),array('id'=>"$game"))[0];
        $return['game']['player1money'] = 39;
        $return['game']['player1income'] = 6;
        $return['game']['player1expenses'] = 0;
        $return['game']['player1revenue'] = 6;
        $return['game']['player2money'] = 40;
        $return['game']['player2income'] = 6;
        $return['game']['player2expenses'] = 0;
        $return['game']['player2revenue'] = 6;
        $board = json_decode($return['game']['board']);

        //initial player tiles
        foreach($board as $rowNum => $row){
            $board[$rowNum][0] = "1";
            // $board[$rowNum][1] = "1";
            // $board[$rowNum][2] = "1";
            // $board[$rowNum][count($row)-3] = "2";
            // $board[$rowNum][count($row)-2] = "2";
            $board[$rowNum][count($row)-1] = "2";
        }

        //initial blank tiles
        for($row=0;$row<=5;$row++){
            for($col=1;$col<=6;$col++){
                $rand = rand(0,100);
                if( $rand >= 30 ){
                    if(
                        ( isset($board[$row+1]) && isset($board[$row+1][$col]) && $board[$row+1][$col]=="blank" )||
                        ( isset($board[$row]) && isset($board[$row][$col+1]) && $board[$row][$col+1]=="blank" )||
                        ( isset($board[$row+1]) && isset($board[$row+1][$col+1]) && $board[$row+1][$col+1]=="blank" )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col]) && $board[$row-1][$col]=="blank" )||
                        ( isset($board[$row]) && isset($board[$row][$col-1]) && $board[$row][$col-1]=="blank" )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col-1]) && $board[$row-1][$col-1]=="blank" )||
                        ( isset($board[$row-1]) && isset($board[$row-1][$col+1]) && $board[$row-1][$col+1]=="blank" )||
                        ( isset($board[$row+1]) && isset($board[$row+1][$col-1]) && $board[$row+1][$col-1]=="blank" )
                    ){
                        continue;
                    }
                    $board[$row][$col] = "blank";
                }
            }
        }
        $return['game']['board'] = json_encode($board);

        update('game',array(
            'player2name'=>$post['player2name'],
            'player2color'=>$post['player2color'],
            'player1money'=>$return['game']['player1money'],
            'player1income'=>$return['game']['player1income'],
            'player1expenses'=>$return['game']['player1expenses'],
            'player1revenue'=>$return['game']['player1revenue'],
            'player2money'=>$return['game']['player2money'],
            'player2income'=>$return['game']['player2income'],
            'player2expenses'=>$return['game']['player2expenses'],
            'player2revenue'=>$return['game']['player2revenue'],
            'turn'=>0,
            'board'=>$return['game']['board']
        ),array('id'=>"$game"));
        $return['message'] = 'createdPlayer2';

    }elseif( isset($_POST['endTurn']) ){
        $id = $_SESSION['game'];
        $_SESSION['game'] = $id;
        $game = select('game',array('*'),array('id'=>"$id"))[0];
        $turn = $game['turn'];
        $player1money = $game['player1money'];
        $player1income = $game['player1income'];
        $player1expenses = $game['player1expenses'];
        $player1revenue = $game['player1revenue'];
        $player2money = $game['player2money'];
        $player2income = $game['player2income'];
        $player2expenses = $game['player2expenses'];
        $player2revenue = $game['player2revenue'];
        $board = json_decode($game['board']);
        if(session_id()==$game['player1session'] && $game['turn'] == 0 && $_POST['endTurn'] == 1 ){
            $nextTurn = 1;

            $owned = findNumByCode("1",$board);
            $lvl1 = findNumByCode("11",$board);
            $lvl2 = findNumByCode("12",$board);

            $player1income = $owned + $lvl1 + $lvl2;
            $player1expenses = $lvl1*3 + $lvl2*9;
            $player1revenue = $player1income - $player1expenses;

            $player1money += $player1revenue;
        }elseif(session_id()==$game['player2session'] && $game['turn'] == 1 && $_POST['endTurn'] == 2){
            $nextTurn = 0;

            $owned = findNumByCode("2",$board);
            $lvl1 = findNumByCode("21",$board);
            $lvl2 = findNumByCode("22",$board);

            $player2income = $owned + $lvl1 + $lvl2;
            $player2expenses = $lvl1*3 + $lvl2*9;
            $player2revenue = $player2income - $player2expenses;

            $player2money += $player2revenue;
        }else{
            echo json_encode(array('message'=>'wait'));
            die;
        }
        foreach($board as $r => $row){
            foreach($row as $c => $cell){
                if (strpos($cell, 'moved') !== false) {
                    $board[$r][$c] = str_replace('moved', '', $board[$r][$c]);
                }
            }
        }
        $game['board'] = json_encode($board);
        $game['player1money'] = $player1money;
        $game['player1income'] = $player1income;
        $game['player1expenses'] = $player1expenses;
        $game['player1revenue'] = $player1revenue;
        $game['player2money'] = $player2money;
        $game['player2income'] = $player2income;
        $game['player2expenses'] = $player2expenses;
        $game['player2revenue'] = $player2revenue;
        $game['turn'] = $nextTurn;
        update(
            'game',
            array(
                'turn'=>$nextTurn,
                'player1money'=>$player1money,
                'player1income'=>$player1income,
                'player1expenses'=>$player1expenses,
                'player1revenue'=>$player1revenue,
                'player2money'=>$player2money,
                'player2income'=>$player2income,
                'player2expenses'=>$player2expenses,
                'player2revenue'=>$player2revenue,
                'board'=>$game['board']
            ),
            array('id'=>"$id")
        );
        $return['game'] = $game;
        $return['message'] = 'turnEnded';
    }elseif( isset($_POST['waitForUpdate']) ){
        //echo 'hi2';die;
        $id = $_SESSION['game'];
        $_SESSION['game'] = $id;
        session_write_close();
        //echo 'newest';
        $return['game'] = select('game',array('*'),array('id'=>"$id"))[0];
        $count = 0;
        while($_POST['waitForUpdate'] == $return['game']['updated'] && $count <= 3){
            usleep(3500000);
            $count++;
            $return['game'] = select('game',array('*'),array('id'=>"$id"))[0];
        }
        $return['timestamp'] = $_POST['waitForUpdate'];
        $return['message'] = 'update';

    }else{
        //echo 'hrere';die;
        $id = $_SESSION['game'];
        $_SESSION['game'] = $id;
        $return['message'] = 'continue';
        $return['game'] = select('game',array('*'),array('id'=>"$id"))[0];
        if($return['game']['player2name'] == "nothing" && $return['game']['player1session'] != session_id() ){
            $return['message'] = 'createPlayer2';
        }
    }

    //if( isset($return['game']) && session_status() == PHP_SESSION_ACTIVE ){ //commented so update will work
        if(
            isset($return['game']) &&
            isset($return['game']['player1session']) &&
            session_id() == $return['game']['player1session']
        ){
            $return['game']['currentPlayer'] = '1';
        }elseif(
            isset($return['game']) &&
            isset($return['game']['player2session']) &&
            session_id() == $return['game']['player2session']
        ){
            $return['game']['currentPlayer'] = '2';
        }
    //}

    echo json_encode($return);
    die;
}

function customSessionStart(){
    ini_set('session.gc_maxlifetime', 31536000);
    ini_set('session.use_cookies', 1);
    session_set_cookie_params(31536000);

    session_start();
}

function browse(){
    echo json_encode(select('game',array('*'),array('full'=>"0")));
}

function joinGame(){
    customSessionStart();
    $game = $_POST['id'];
    $_SESSION['game'] = $game;
    update('game',array(
        'player2session'=>session_id(),
        'full'=>'1'
    ),array('id'=>"$game"));
    echo "joined";
}

function newGame(){
    customSessionStart();
    unset($_SESSION['game']);
}

function findNumByCode($player,$board){
    $count = 0;
    foreach($board as $row){
        foreach($row as $cell){
            if(
                $cell == $player ||
                $cell == $player."moved"
            ){
                $count++;
            }
        }
    }
    return $count;
}
