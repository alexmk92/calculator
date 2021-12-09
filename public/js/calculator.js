document.addEventListener("DOMContentLoaded", function () {
  const display = document.querySelector(".calculator .display");
  const buttons = document.querySelectorAll(".calculator input[type=button]");
  const expression = document.querySelector("#expression");
  const historyNodes = document.querySelectorAll("#history li");

  let initialized = display.textContent != 0;

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

  historyNodes.forEach((historyNode) => {
    historyNode.addEventListener("click", (e) => {
      let node = e.target;
      // This is gross and not super versatile but assume the structure of
      // the past calculation nodes is <ul><li><span>1+1</span><h2>2</h2></li></ul>
      // we will traverse up the tree until we hit the LI
      while (node.nodeName.toLowerCase() !== "li") {
        node = node.parentNode;
      }

      updateDisplay(node.firstElementChild.textContent, true);
    });
  });
});
