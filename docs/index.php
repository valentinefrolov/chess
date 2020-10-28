<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chess</title>
</head>
<body>
    <div class="wrapper">
        <?php

        /*$isBlack = true;
        $rows = [];
        $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $num = ['8', '7', '6', '5', '4', '3', '2', '1'];
        $opt = ['black', 'white'];
        $index = 0;

        if($isBlack) {

            $abc = array_reverse($abc);
            $num = array_reverse($num);
            $index = 1;
            $opt = ['white', 'black'];
        }


        for($i = 0; $i <= 9; $i++) {
            $stroke = [];
            if($i == 0 || $i == 9) {
                $stroke = array_merge([''], $abc, ['']);
            } else {
                $line = [];
                for($j = 0; $j < count($num); $j++) {
                    if($isBlack) {
                        if ($i % 2 == 0) {
                            $line[] = $j % 2 == 0 ? $opt[1] : $opt[0];
                        } else {
                            $line[] = $j % 2 == 0 ? $opt[0] : $opt[1];
                        }
                    } else {
                        if ($i % 2 == 0) {
                            $line[] = $j % 2 == 0 ? $opt[0] : $opt[1];
                        } else {
                            $line[] = $j % 2 == 0 ? $opt[1] : $opt[0];
                        }
                    }
                }
                $stroke = array_merge([$num[$i-1]], $line, [$num[$i-1]]);
            }
            $rows[] = $stroke;
        }*/

        ?>
        <div class="board" id="Board">
            <table class="board__table">
                <?php foreach($rows as $v => $row) :?>
                <tr>
                    <?php foreach($row as $h => $cell): ?>
                        <?php if($cell == ''): ?>
                            <td class="board__edge"></td>
                        <?php elseif($cell == 'black'): ?>
                            <td class="board__cell board__cell_black" data-cell="<?=($h-1).($v-1)?>"></td>
                        <?php elseif($cell == 'white'): ?>
                            <td class="board__cell board__cell_white" data-cell="<?=($h-1).($v-1)?>"></td>
                        <?php else :?>
                            <td class="board__edge"><?=$cell?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>
<script>
    var initUrl = '/init.php<?= !empty($_GET['game_id']) ? '?game_id='.$_GET['game_id'] : '' ?>';
    var moveUrl = '/move.php<?= !empty($_GET['game_id']) ? '?game_id='.$_GET['game_id'] : '' ?>';
</script>
<link rel="stylesheet" href="/css/index.css"/>
<script src="/js/app.bundle.js"></script>
</html>
