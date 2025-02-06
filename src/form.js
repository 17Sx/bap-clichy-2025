let btn = document.getElementsByClassName("change")[0];
let connexion = document.getElementsByClassName("connexion")[0];
let inscription = document.getElementsByClassName("inscription")[0];

let form = document.getElementsByClassName("connexion")[0];

let user_input = document.getElementsByName("user")[0];

let action = document.getElementsByClassName("action")[0];
let title = document.getElementsByClassName("title")[0];
let accroche = document.getElementsByClassName("accroche")[0];
let error = document.getElementsByClassName("error")[0];

let compte = 0;

btn.addEventListener("click", function () {
  if (compte == 0) {
    compte++;

    if (error) {
      error.style.display = "none";
    }
    connexion.style.transform = "translateX(100%)";
    connexion.style.borderRadius = "0 1vw 1vw 0";

    inscription.style.transform = "translateX(-100%)";
    inscription.style.borderRadius = " 1vw 0 0 1vw";

    user_input.style.display = "block";
    form.action = "inscription.php";

    action.innerHTML = "S'inscrire :";
    title.innerHTML = "Vous avez déjà un compte ?";
    accroche.innerHTML = "Venez vous connecter rapidement";
    btn.innerHTML = "Se connecter";
  } else {
    compte--;
    connexion.style.transform = "translateX(0%)";
    connexion.style.borderRadius = "1vw 0 0 1vw";

    inscription.style.transform = "translateX(0%)";
    inscription.style.borderRadius = "0 1vw 1vw 0";

    user_input.style.display = "none";
    form.action = "connexion.php";

    action.innerHTML = "Connexion :";
    title.innerHTML = "Pas de compte ?";
    accroche.innerHTML =
      "Créer vous un compte gratuitement et commencer a utiliser CanneLife";
    btn.innerHTML = "Rejoignez-nous";
  }
});
