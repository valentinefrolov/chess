import Vue from 'vue';

import IntroWindow from './window/intro';
import MainWindow from './window/main';
import {Game} from "./game";

new Vue({
    el: '#Test',
    data: {
        gameMode: 0,
        Colors: [
            'white',
            'black'
        ],
        GameModes: [
            {
                value: 1,
                title: 'Play alone'
            },
            {
                value: 2,
                title: 'Play internet'
            }
        ],
        color: '',
        boardData: null,
        chooseName: false,
        gameLink: ''
    },
    methods: {
        setGameMode(e) {
            this.gameMode = e.target.value;
            if(this.color && this.gameMode === '1') {
                this.startSingleGame();
            }
        },
        setColor(e) {
            this.color = e.target.value;
            if(this.gameMode === '1') {
                this.startSingleGame();
            }
        },
        startSingleGame() {
            const game = new Game(this.color);
            game.singleGame(this.drawBoard);
        },
        drawBoard(response) {
            this.boardData = response.board;
        }
    },
    template: `
        <div class="wrapper">
            <intro-window v-if="!boardData" :onChangeMode="setGameMode" :gameModes="GameModes" :colors="Colors" :onChangeColor="setColor"/>
            <main-window :rows="boardData"/>
        </div>
    `,
    components: {
        'intro-window' : IntroWindow,
        'main-window' : MainWindow,
    }
});




/*import {Game} from './game';

const game = new Game();
game.init();*/
