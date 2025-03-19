document.addEventListener("DOMContentLoaded", () => {
  const questionTitles = document.querySelectorAll(".question_title");

  questionTitles.forEach((title) => {
    // Cache tous les paragraphes au chargement
    const question = title.parentElement;
    const paragraph = question.querySelector("p");
    const chevron = title.querySelector("img");

    paragraph.style.display = "none";

    title.addEventListener("click", () => {
      // Toggle du paragraphe avec animation
      if (paragraph.style.display === "none") {
        paragraph.style.display = "block";
        chevron.style.transform = "rotate(180deg)";
      } else {
        paragraph.style.display = "none";
        chevron.style.transform = "rotate(0deg)";
      }
    });
  });
});
