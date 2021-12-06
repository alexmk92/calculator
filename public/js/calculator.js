document.addEventListener("DOMContentLoaded", function () {
  const display = document.querySelector(".calculator .display");
  const buttons = document.querySelectorAll(".calculator input[type=button]");
  const expression = document.querySelector("#expression");

  let initialized = display.value != 0;

  const clearDisplay = () => {
    updateDisplay(0, true);
    initialized = false;
  };

  const updateDisplay = (value, clear = false) => {
    if (clear || !initialized) {
      display.innerHTML = "";
    }

    display.innerHTML += value;
    expression.value = display.innerHTML;
    initialized = true;
  };

  const calculatorInteractionEvent = (e) => {
    var input = e.target.value;

    switch (input.toLowerCase()) {
      case "c":
        clearDisplay();
        break;
      case "=":
        return true;
      default:
        updateDisplay(input);
    }
  };

  buttons.forEach((button) => {
    button.addEventListener("click", calculatorInteractionEvent);
  });
});
