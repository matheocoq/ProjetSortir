class Fetch {
    static async get(url) {
        const response = await fetch(url)
        const data = await response.json()
        return data
    }
}

const init = async () => {
    const res = await Fetch.get('http://localhost/ProjetSortir/public/api/lieux')
    const sorties_lieux = document.getElementById('sorties_lieux');
    sorties_lieux.addEventListener('change', (e) => {
        const idLieuxSelected = e.target.value

    })
}

init()