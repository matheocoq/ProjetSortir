class Fetch {
    static async get(url) {
        const response = await fetch(url)
        const data = await response.json()
        return data
    }
}



const init = async () => {
    const sorties_lieux = document.getElementById('sorties_lieux');
    const idLieuxBase = sorties_lieux.value ? parseInt(sorties_lieux.value) : 1
    const villeIn = document.getElementById('ville');
    const rueIn = document.getElementById('rue');
    const cpIn = document.getElementById('cp');
    const latitudeIn = document.getElementById('latitude');
    const longitudeIn = document.getElementById('longitude');

    const handleChange = (e) => {
        const idLieuxSelected = parseInt(e.target.value)
        res.forEach(el => {
            if (el.idLieux === idLieuxSelected) {
                villeIn.value = el.nomVille
                rueIn.value = el.rue
                cpIn.value = el.codePostal
                latitudeIn.value = el.latitude
                longitudeIn.value = el.longitude
            }
        })
    }

    handleChange({target: {value: idLieuxBase}})

    sorties_lieux.addEventListener('change', handleChange)
}

init()
    .then()
    .catch(e => {
        console.error(e)
    })