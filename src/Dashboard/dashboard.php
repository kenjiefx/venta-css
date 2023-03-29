<?php 

use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;
use Kenjiefx\VentaCSS\Factories\VentaConfigFactory;


$Dashboard = new VentaDashboard();
(new AssetsManager(new VentaConfigFactory(),$Dashboard))->compileAssets();
$Selectors = $Dashboard->export();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Venta Dashboard</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600&display=swap" rel="stylesheet"> 
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <style>
            <?php include __dir__.'/styles.css'; ?>
        </style>
        <script type="text/javascript">
            <?php include __dir__.'/script.js'; ?>
        </script>
    </head>
    <body>
        <header></header>
        <main>
            <aside>
                <ul>
                    <?php 
                        $selectorViewPointer = 0;
                        foreach($Selectors as $groupName => $groups) {
                            echo '<li>';
                            echo '<div class="group-name">'.$groupName.'</div>';
                            echo '<div><ul>';
                            foreach ($groups as $selectorName => $selectorTypes) {
                                echo '<li class="group-item" data-pointer-id="'.$selectorViewPointer++.'">'.$selectorName.'</li>';
                            }
                            echo '</ul></div>';
                            echo '</li>'; 
                        }
                    ?>
                </ul>
            </aside>
            <section class="page-body">
                
                <?php 
                    $selectorViewPointer = 0;
                    foreach($Selectors as $groupName => $groups) {
                        foreach ($groups as $selectorName => $selectorTypes) {
                            echo '<div class="selector-view" data-selector-pointer="'.$selectorViewPointer++.'">';
                                echo '<div class="selector-view-header">';
                                    echo '<h1>'.$selectorName.'</h1>';
                                echo '</div>';
                                echo '<hr>';
                                echo '<div class="table-wrapper">';
                                    echo '<div class="table">';
                                        foreach ($selectorTypes['variants'] as $variantName => $variantValue) {
                                            echo '<div class="table-row">';
                                                echo '<div class="table-column selector-variant-name">'.$variantName.'</div>';
                                                echo '<div class="table-column selector-variant-name">'.$variantValue.'</div>';
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        }
                    }
                ?>
            </section>    
        </main>
        <footer></footer>
    </body>
</html>
