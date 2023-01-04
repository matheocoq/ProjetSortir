class Fetch {
    static async get(url) {
        const response = await fetch(url)
        const data = await response.json()
        return data
    }
}



const init = async () => {
    const res = await Fetch.get('/api/lieux')
    const sorties_lieux = document.getElementById('sorties_lieux');
    const villeIn = document.getElementById('ville');
    const rueIn = document.getElementById('rue');
    const cpIn = document.getElementById('cp');
    const latitudeIn = document.getElementById('latitude');
    const longitudeIn = document.getElementById('longitude');

    const handleChange = (e) => {
        const idLieuxSelected = e ? parseInt(e.target.value) : 1
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

    handleChange()

    sorties_lieux.addEventListener('change', handleChange)
}

init()
    .then()
    .catch(e => {
        console.error(e)
    })