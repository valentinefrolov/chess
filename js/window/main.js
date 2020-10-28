export default {
    props: {
        rows: Array,
    },
    template: `
        <div class="board" id="Board">
            <table class="board__table">
                <tr v-for="(row, rowIndex) of rows">
                    <template v-for="(cell, cellIndex) of row">
                        <td v-if="cell === ''" class="board__edge"/>
                        <td v-else-if="cell === 'black'" class="board__cell board__cell_black" :data-cell="(cellIndex-1) + ' ' + (rowIndex-1)"/>
                        <td v-else-if="cell === 'white'" class="board__cell board__cell_white" :data-cell="(cellIndex-1) + ' ' + (rowIndex-1)"/>
                        <td v-else class="board__edge">{{cell}}</td>
                    </template>
                </tr>
            </table>
        </div>
    `

}
