<?php

return [
    ['GET', '/', ['GoPokedex\Controllers\Homepage', 'show']],
    ['POST', '/login', ['GoPokedex\Controllers\User', 'doLogIn']],
    ['GET', '/logout', ['GoPokedex\Controllers\User', 'doLogOut']],
    ['POST', '/register', ['GoPokedex\Controllers\User', 'doRegister']],
    ['POST', '/code-login', ['GoPokedex\Controllers\User', 'doCodeLogIn']],
    ['POST', '/code-register', ['GoPokedex\Controllers\User', 'doCodeRegister']],
    ['GET', '/verify', ['GoPokedex\Controllers\User', 'doVerify']],
    ['POST', '/reconfirm', ['GoPokedex\Controllers\User', 'doReconfirm']],
    ['GET', '/pokedex', ['GoPokedex\Controllers\Pokedex', 'getPokedex']],
    ['POST', '/pokedex/update', ['GoPokedex\Controllers\Pokedex', 'doUpdate']],
    ['GET', '/news', ['GoPokedex\Controllers\News', 'getNews']],

    ['GET', '/admin', ['GoPokedex\Controllers\Admin', 'showDashboard']],
    ['GET', '/admin/pokemon', ['GoPokedex\Controllers\Admin', 'updatePokemon']]
];
