export class Figure {

    constructor(color, figure, id, x, y) {
        this.color = color;
        this.type = figure;
        this.id = id;
        this.x = x;
        this.y = y;
        this.object = null;
    }

    render(board) {
        this.object = $('<div data-figure="'+this.color+'" class="figure figure_'+this.color+' figure_'+this.color+'_'+this.type+'"/>');
        const cell = board.find('[data-cell="'+this.x+' '+this.y+'"]');
        const pos = cell.position();
        this.object.css({left: pos.left, top: pos.top});
        board.append(this.object);
    }

    updatePosition(board)
    {
        const cell = board.find('[data-cell="'+this.x+' '+this.y+'"]');
        const pos = cell.position();
        this.object.css({left: pos.left, top: pos.top});
    }

    kill() {
        this.object.remove();
    }


}
