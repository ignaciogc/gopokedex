var app = new Vue({
    el: '#app',
    data: {
        pokedex: [],
        myPokemon: null,
        filters: {
            legendary: false,
            shiny: false,
            regional: false,
            oor: false
        },
        myFilters: {
            hasSeen: false,
            hasCaught: false,
            hasShiny: false,
            hasPerfect: false,
            hasImperfect: false,
            hasLucky: false,
            notSeen: false,
            notCaught: false,
            notShiny: false,
            notPerfect: false,
            notImperfect: false,
            notLucky: false
        },
        region: null,
        search: "",
        counts: {
            total: 0,
            kanto: 0,
            johto: 0,
            hoenn: 0,
            sinnoh: 0,
            legendary: 0,
            shiny: 0,
            regional: 0,
            oor: 0,
            hasSeen: 0,
            hasCaught: 0,
            hasShiny: 0,
            hasPerfect: 0,
            hasImperfect: 0,
            hasLucky: 0,
            notSeen: 0,
            notCaught: 0,
            notShiny: 0,
            notPerfect: 0,
            notImperfect: 0,
            notLucky: 0
        },
        news: [],
        last_update: '',
        modals: {
            register: false,
            login: false,
            code: false,
            verification: false,
            details: false
        }
    },
    delimiters: ["[[", "]]"],
    created: function() {
        this.getPokedex();
        this.getNews();
    },
    methods: {
        updatePokemon: function (pokemon, type, value = null) {
            var changed = (pokemon.user[type] !== value);
            var inverse = type.replace("has", "not");
            pokemon.user[type] = (value === null) ? !pokemon.user[type] : value;
            if (pokemon.user[type]) {
                if (changed) {
                    this.counts[type]++;
                    this.counts[inverse]--;
                }
                switch (type) {
                    case 'hasSeen':
                        break;
                    case 'hasCaught':
                        this.updatePokemon(pokemon, 'hasSeen', true);
                        break;
                    default:
                        this.updatePokemon(pokemon, 'hasCaught', true);
                }
            } else {
                if (changed) {
                    this.counts[type]--;
                    this.counts[inverse]++;
                }
                switch (type) {
                    case 'hasSeen':
                        this.updatePokemon(pokemon, 'hasCaught', false);
                        break;
                    case 'hasCaught':
                        this.updatePokemon(pokemon, 'hasPerfect', false);
                        this.updatePokemon(pokemon, 'hasImperfect', false);
                        this.updatePokemon(pokemon, 'hasShiny', false);
                        this.updatePokemon(pokemon, 'hasLucky', false);
                }
            }

            if (value === null) {
                this.updatePokedex(pokemon);
            }
        },
        updatePokemonForms: function (pokemon, form, force = true) {
            if (pokemon.user['hasForms'].includes(form)) {
                // remove the form from the user array
                var pos = pokemon.user['hasForms'].indexOf(form);
                pokemon.user['hasForms'].splice(pos, 1);
                if (pokemon.user['hasShinyForms'].includes(form)) {
                    this.updatePokemonShinyForms(pokemon, form, false);
                }
            } else {
                // add the form to the user array
                pokemon.user['hasForms'].push(form);
                this.updatePokemon(pokemon, 'hasCaught', true);
            }

            if (force) {
                this.updatePokedex(pokemon);
            }
        },
        updatePokemonShinyForms: function (pokemon, form, force = true) {
            if (pokemon.user['hasShinyForms'].includes(form)) {
                // remove the form from the user array
                var pos = pokemon.user['hasShinyForms'].indexOf(form);
                pokemon.user['hasShinyForms'].splice(pos, 1);
            } else {
                // add the form to the user array
                pokemon.user['hasShinyForms'].push(form);
                if (!pokemon.user['hasForms'].includes(form)) {
                    this.updatePokemonForms(pokemon, form, false);
                }
            }

            if (force) {
                this.updatePokedex(pokemon);
            }
        },
        updatePokedex: function(pokemon) {
            this.$http.post(
                '/pokedex/update',
                {
                    pokemon: pokemon.dex,
                    user: pokemon.user
                },
                {
                    emulateJSON: true
                }
            ).then(response => {
                //console.log(response);
            }, response => {
                console.log(response);
            });
        },
        updateFilter: function (selected) {
            if(typeof this.filters[selected] === 'undefined') return;
            var icon = document.querySelector("[data-filter='"+selected+"'] i");
            icon.classList.toggle('ion-md-square-outline');
            icon.classList.toggle('ion-md-checkbox');

            this.filters[selected] = !this.filters[selected];
        },
        updateMyFilter: function (selected) {
            if(typeof this.myFilters[selected] === 'undefined') return;
            var icon = document.querySelector("[data-filter='"+selected+"'] i");
            icon.classList.toggle('ion-md-square-outline');
            icon.classList.toggle('ion-md-checkbox');

            this.myFilters[selected] = !this.myFilters[selected];
        },
        updateRegion: function (selected) {
            var icons = document.querySelectorAll('#regions i');
            icons.forEach(function(icon) {
                icon.classList.remove('ion-md-radio-button-on');
                icon.classList.add('ion-md-radio-button-off');
            });

            if(this.region == selected) {
                this.region = null;
            } else {
                this.region = selected;
                var icon = document.querySelector("[data-filter='"+selected+"'] i");
                icon.classList.remove('ion-md-radio-button-off');
                icon.classList.add('ion-md-radio-button-on');
            }
        },
        getCounts: function (event) {
            for (let i = 0; i < this.pokedex.length; i++) {
                if (this.pokedex[i].available) {
                    this.counts.total++;
                    this.counts[this.pokedex[i].region.toLowerCase()]++;
                    if (this.pokedex[i].legendary) {
                        this.counts.legendary++;
                    }
                    if (this.pokedex[i].shiny) {
                        this.counts.shiny++;
                    }
                    if (this.pokedex[i].regional) {
                        this.counts.regional++;
                    }
                    if (this.pokedex[i].oor) {
                        this.counts.oor++;
                    }
                    if (this.pokedex[i].user.hasSeen) {
                        this.counts.hasSeen++;
                    } else {
                        this.counts.notSeen++;
                    }
                    if (this.pokedex[i].user.hasCaught) {
                        this.counts.hasCaught++;
                    } else {
                        this.counts.notCaught++;
                    }
                    if (this.pokedex[i].user.hasShiny) {
                        this.counts.hasShiny++;
                    } else {
                        this.counts.notShiny++;
                    }
                    if (this.pokedex[i].user.hasPerfect) {
                        this.counts.hasPerfect++;
                    } else {
                        this.counts.notPerfect++;
                    }
                    if (this.pokedex[i].user.hasImperfect) {
                        this.counts.hasImperfect++;
                    } else {
                        this.counts.notImperfect++;
                    }
                    if (this.pokedex[i].user.hasLucky) {
                        this.counts.hasLucky++;
                    } else {
                        this.counts.notLucky++;
                    }
                }
            }
        },
        getPokedex: function (event) {
            this.$http.get(
                '/pokedex'
            ).then(response => {
                this.pokedex = response.body;
                this.getCounts();
            }, response => {
                console.log(response);
            });
        },
        getNews: function (event) {
            this.$http.get(
                '/news'
            ).then(response => {
                this.news = response.body;
                this.getLatest();
            }, response => {
                console.log(response);
            });
        },
        getLatest: function (event) {
            var last = 0;
            for (let i = 0; i < this.news.length; i++) {
                if (this.news[i].type == 'pokedex_update' && this.news[i].id > last) {
                    last = this.news[i].id;
                    this.last_update = this.news[i].created_date.substring(0,10);
                }
            }
        },
        pokemonDetails: function (pokemon) {
            this.myPokemon = pokemon;
            this.modals.details = true;
        }
    }
})

document.addEventListener('DOMContentLoaded', () => {
    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {
        // Add a click event on each of them
        $navbarBurgers.forEach( el => {
            el.addEventListener('click', () => {
                // Get the target from the "data-target" attribute
                const target = el.dataset.target;
                const $target = document.getElementById(target);

                // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
    }
});
