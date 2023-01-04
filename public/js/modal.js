$( ".openModalAnnulation" ).click(function() {
    var modal = document.getElementById("modalAnnulation");
    console.log()
    document.getElementById("modal-identifiant").value = $(this).data("identifiant")
    modal.style.display = "block";
});

function closeModalAnnulation() {
    var modal = document.getElementById("modalAnnulation");
    modal.style.display = "none";
  }