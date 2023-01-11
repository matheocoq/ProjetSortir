$( ".openModalAnnulation" ).click(function() {
    var modal = document.getElementById("modalAnnulation");
    document.getElementById("modal-identifiant").value = $(this).data("identifiant")
    document.getElementById("modal-nom").innerHTML = "Annuler la sortie "+$(this).data("nom")
    modal.style.display = "block";
});

function closeModalAnnulation() {
    var modal = document.getElementById("modalAnnulation");
    modal.style.display = "none";
  }