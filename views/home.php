<div class="container main">
    <div id="create">
        <div class="modal">
            <input id="p1n" type="textbox" />
            <input id="p1c" type="color" />
            <input id="newGameSubmit" type="submit" />
        </div>
    </div>
    <div id="game-board">
        <div class="verticle-aligner">
            <div class="castleArea">
                <div class="flex_vertical_align"><p class="playerAction player1actions" id="player1buyPerson">Buy Person ($20)</p></div>
                <div class="flex_vertical_align"><p class="playerAction player1actions" id="player1endTurn">End Turn</p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player1name"></p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player1money"></p></div>
                <div class="flex_vertical_align"><div class="castle" id="castle1"><div class="minLineHeight">.</div></div></div>

                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player1income"></p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player1expenses"></p></div>
                <div class="border-bottom"></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player1revenue"></p></div>
                <div class="flex_vertical_align"><div class="playerinfo playerTurn" id="player1turn"></div></div>
            </div>
            <div id="spots"></div>
            <div class="castleArea">
                <div class="flex_vertical_align"><p class="playerAction player2actions" id="player2buyPerson">Buy Person ($20)</p></div>
                <div class="flex_vertical_align"><p class="playerAction player2actions" id="player2endTurn">End Turn</p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player2name"></p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player2money"></p></div>
                <div class="flex_vertical_align"><div class="castle" id="castle2"><div class="minLineHeight">.</div></div></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player2income"></p></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player2expenses"></p></div>
                <div class="border-bottom"></div>
                <div class="flex_vertical_align smallFlex"><p class="playerinfo" id="player2revenue"></p></div>
                <div class="flex_vertical_align"><div class="playerinfo playerTurn" id="player2turn"></div></div>
            </div>
        </div>
    </div>
</div>
