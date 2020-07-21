<html>
    <head>
        <link rel="shortcut icon" href="style/favicon.png" type="image/png" />
        <link rel="icon" href="style/favicon.png" type="image/png" />
        <link rel="stylesheet" href="style/style.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="style/minecraft-webfont.css" />
        <title>SethBling's Loot-Table Randomizer.</title>
    </head>
    <body>
        <div id="content">
            <h1>SethBling's Loot-Table Randomizer.</h1>
            <form action="loot_table_randomizer.php" method="post">
                <div id="inputs">
                    <input type="number" autocomplete="off" name="seed" placeholder="(optional) Enter seed here" />
                    <input type="submit" value="Generate"/>
                    <br>
                    <div id="options">
                        <label class="toggle">
                            <input type="checkbox" name="randomLootTable[]" value="blocks" checked/>
                            Randomize Blocks:
                            <span class="state"></span>
                        </label>
                        <label class="toggle">
                            <input type="checkbox" name="randomLootTable[]" value="chests" checked/>
                            Randomize Chests:
                            <span class="state"></span>
                        </label>
                        <label class="toggle">
                            <input type="checkbox" name="randomLootTable[]" value="entities" checked/>
                            Randomize Entities:
                            <span class="state"></span>
                        </label>
                        <label class="toggle">
                            <input type="checkbox" name="randomLootTable[]" value="gameplay" checked/>
                            Randomize Gameplay:
                            <span class="state"></span>
                        </label>
                    </div>
                </div>
            </form>
            <p id="text">
                How to use:
                1. Let your loot table generate.
                2. Go into <a href="https://help.mojang.com/customer/portal/articles/1480874">your '.minecraft' directory</a>.
                3. Go into 'saves' and then into the world's folder, where you want the drops to be randomized.
                4. Go into 'datapacks' and place the generated zip file in this folder.
                5. Load the world in Minecraft and go nuts!<br>
                For questions, contact me on <a href="https://fasguy.net/discord" target="_blank" rel="noreferrer">Discord</a>.
            </p>
            <p>
                Minecraft © Mojang 2009 - <?php echo date("Y"); ?>. I am not affiliated with Mojang or SethBling in any way.
            </p>
        </div>
        <div id="footer">
            <div id="version">
                <a href="https://github.com/Fasguy/sb-ltr" target="_blank" rel="noreferrer">Version 1.1.3</a>
            </div>
            <div id="additional">
                <a href="https://www.youtube.com/user/sethbling" target="_blank" rel="noreferrer">SethBling's YouTube Channel</a>
                <a href="https://www.youtube.com/user/FasguyLP" target="_blank" rel="noreferrer">Fasguy's YouTube Channel</a>
            </div>
        </div>
    </body>
</html>