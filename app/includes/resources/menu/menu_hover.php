<?php if ($_SESSION['share_acc'] == 0) { ?>
<table class='in_menu'>
    <tr>
        <td>
            <div class='menu_title'>Social</div>
             <ul>
                <li><a href='./friends-add'>Buscar Treinadores</a></li>
                <li><a href='./attack/duel/invite'>Desafiar Treinadores</a></li>
                <li><a href='./friends'>Meus Amigos</a></li>
            </ul>
        </td>
        <td>
            <div class='menu_title'>Extras</div>
             <ul>
                <li><a href='./badges'>Insígnias</a></li>
                <li><a href='./fishing'>Pescaria</a></li>
                <li><a href='./pokedex'>PokéDex</a></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td>
            <div class='menu_title'>Assistência</div>
             <ul>
                <li><a href='./calculator'>Calculadora</a></li>
                <li><a href='./information'>Guia Pokémon</a></li>
                <li><a href='./juiz'>Juiz Pokémon</a></li>
            </ul>
        </td>
        <td>
            <div class='menu_title'>Outros</div>
             <ul>
                <li><a href='./house-seller'>Comprar Casa</a></li>
                <li><a href='./specialists'>Especialistas Pokémon</a></li>
                <li><a href='./statistics'>Estatísticas Gerais</a></li>
            </ul>
        </td>
    </tr>
</table>
<?php } else { ?>

<table class='in_menu'>
    <tr>
        <td>
            <div class='menu_title'>Acessível</div>
             <ul>
                <li><a href='./statistics'>Estatísticas Gerais</a></li>
                <li><a href='./information'>Guia Pokémon</a></li>
                <li><a href='./badges'>Insígnias</a></li>
                <li><a href='./pokedex'>PokéDex</a></li>
            </ul>
        </td>
    </tr>
</table>

<?php } ?>