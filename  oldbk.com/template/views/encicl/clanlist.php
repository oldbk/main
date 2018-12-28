<div class="mt-3">
    <h3>ส๋เํ๛ ฮ๋ไมส</h3>
    <ul>
        <?php
        if (count($clans)) {
            foreach($clans as $clan) {
                echo $this->renderPartial("common/renderclan",
                    ['clan' => $clan]
                );
            }
        }
        ?>
    </ul>
</div>