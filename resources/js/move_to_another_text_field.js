document.addEventListener("keydown", function(event) {
    if (event.key === "Tab" || event.key === "Enter" || event.key === "ArrowDown" || event.key === "ArrowUp") {
        event.preventDefault();
        const activeElement = document.activeElement;
        const numberInputs = Array.from(document.querySelectorAll("input[type=number]"));
        const currentIndex = numberInputs.indexOf(activeElement);

        if (event.key === "Tab" || event.key === "Enter" || event.key === "ArrowDown") {
            const nextIndex = currentIndex === numberInputs.length - 1 ? 0 : currentIndex + 1;
            numberInputs[nextIndex].select();
        } else {
            const previousIndex = currentIndex === 0 ? numberInputs.length - 1 : currentIndex - 1;
            numberInputs[previousIndex].select();
        }
    }
});

// Add a click event listener to select all text when the input is clicked.
const numberInputs = document.querySelectorAll("input[type=number]");
numberInputs.forEach(input => {
    input.addEventListener("click", function() {
        this.select();
    });
});
