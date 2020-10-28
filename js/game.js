import {Figure} from "./figure";

export class Game {

    constructor(color) {
        this.active = 'white';
        this.color = color;
        this.action = {
            figure: null,
            left: 0,
            top: 0,
            offsetLeft: 0,
            offsetTop: 0,
        };
        this.figures = [];
        this.connection = null;
        this.callback = null;

        $(window).on('resize orientationchange', () => {
            const board = $('#Board');
            for(let i = 0; i < this.figures.length; i++) {
                this.figures[i].updatePosition(board);
            }
        });


        this.connection = new WebSocket('ws://' + window.wsHost);
        this.connection.onmessage = (e) => {
            try {
                const data = JSON.parse(e.data);
                if(data.action === 'CREATE_SINGLE_GAME') {
                    if(this.callback) {
                        this.callback(data);
                        this.callback = null;
                    }
                    setTimeout(() => {
                        const board = $('#Board');
                        for (let color in data.players) {
                            if (data.players.hasOwnProperty(color)) {
                                const figures = data.players[color];
                                for (let j = 0; j < figures.length; j++) {
                                    const figure = new Figure(color, figures[j].class, figures[j].id, figures[j].x, figures[j].y);
                                    figure.render(board);
                                    this.figures.push(figure);
                                }
                            }
                        }
                    }, 50);
                } else if(data.action === 'MOVE_FIGURE') {
                    this.active = data.active;
                    let toKill = -1;
                    for(let i = 0; i < data.figures.length; i++) {
                        for(let j=0; j < this.figures.length; j++) {
                            if(this.figures[j].id === data.figures[i].id) {
                                if(data.figures[i].action === 'move' || data.figures[i].action === 'error') {
                                    const cell = $('#Board').find('[data-cell="'+ data.figures[i].x +' '+ data.figures[i].y +'"]');
                                    //cell.css('background', 'red');
                                    const pos = cell.position();
                                    this.finishMove(pos.left, pos.top);
                                    if(data.figures[i].action === 'move') {
                                        this.figures[j].x = data.figures[i].x;
                                        this.figures[j].y = data.figures[i].y;
                                    }
                                } else if(data.figures[i].action === 'kill') {
                                    this.figures[j].kill();
                                    toKill = j;
                                } else if(data.figures[i].action === 'check') {
                                    console.log(data.figures[i]);
                                } else if(data.figures[i].action === 'mate') {
                                    alert('mate');
                                }
                            }
                        }
                    }
                    if(toKill !== -1) {
                        this.figures.splice(toKill, 1);
                    }
                }
            } catch(e) {
                if(game.action.figure) {
                    this.finishMove(game.action.left, game.action.top);
                }
            }
        };


        const game = this;
        $(document).on('mousedown', '[data-figure]', function (e) {
            const figure = $(this);
            if (figure.data('figure') !== game.active) return;
            for (let i = 0; i < game.figures.length; i++) {
                if (game.figures[i].object.is(figure)) {
                    figure.addClass('active');
                    const pos = figure.position();
                    game.action.figure = game.figures[i];
                    game.action.left = pos.left;
                    game.action.offsetLeft = pos.left - e.clientX;
                    game.action.top = pos.top;
                    game.action.offsetTop = pos.top - e.clientY;
                    break;
                }
            }
        });

        $(document).on('mousemove', function (e) {
            if (!game.action.figure) return;
            game.action.figure.object.css({
                left: e.clientX + game.action.offsetLeft,
                top: e.clientY + game.action.offsetTop
            });
        });

        $(document).on('mouseup', () => {
            if (!game.action.figure) return;
            const pos = game.action.figure.object.position();
            const centerX = pos.left + game.action.figure.object.width() / 2;
            const centerY = pos.top + game.action.figure.object.height() / 2;

            const cells = $('[data-cell]');
            let found = false;

            cells.each(function () {
                const cell = $(this);
                const pos = cell.position();
                if (
                    centerX >= pos.left && centerX <= pos.left + cell.outerWidth(true)
                    &&
                    centerY >= pos.top && centerY <= pos.top + cell.outerHeight(true)
                ) {
                    found = true;
                    const coords = cell.data('cell').toString().split(' ');
                    game.connection.send('MOVE_FIGURE:'+game.action.figure.id+':'+coords[0]+':'+coords[1]);
                }
            });

            if (!found) {
                game.finishMove(game.action.left, game.action.top);
            }

        });

    }

    finishMove(left, top)
    {
        this.action.figure.object.css({left: left, top: top});
        this.action.figure.object.removeClass('active');
        this.action.figure = null;
        this.action.left = 0;
        this.action.offsetLeft = 0;
        this.action.top = 0;
        this.action.offsetTop = 0;
    }

    singleGame(callback) {
        this.callback = callback;
        this.connection.onopen = () => {
            this.connection.send('CREATE_SINGLE_GAME:' + this.color);
        };

    }

}
