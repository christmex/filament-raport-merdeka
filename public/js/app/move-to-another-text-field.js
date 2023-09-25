document.addEventListener("keydown", function(event) {
    if (event.key === "Tab") {
        event.preventDefault();
        const activeElement = document.activeElement;
        const numberInputs = Array.from(document.querySelectorAll("input[type=number]"));
        const currentIndex = numberInputs.indexOf(activeElement);

        if (event.shiftKey) {
            const previousIndex = currentIndex === 0 ? numberInputs.length - 1 : currentIndex - 1;
            numberInputs[previousIndex].focus();
        } else {
            const nextIndex = currentIndex === numberInputs.length - 1 ? 0 : currentIndex + 1;
            numberInputs[nextIndex].focus();
        }
    }

    if (event.key === "Enter") {
        event.preventDefault();
        const activeElement = document.activeElement;
        const numberInputs = Array.from(document.querySelectorAll("input[type=number]"));
        const currentIndex = numberInputs.indexOf(activeElement);

        if (event.shiftKey) {
            const previousIndex = currentIndex === 0 ? numberInputs.length - 1 : currentIndex - 1;
            numberInputs[previousIndex].focus();
        } else {
            const nextIndex = currentIndex === numberInputs.length - 1 ? 0 : currentIndex + 1;
            numberInputs[nextIndex].focus();
        }
    }

    if (event.key === "ArrowDown" || event.key === "ArrowUp") {
        event.preventDefault();
        const activeElement = document.activeElement;
        const numberInputs = Array.from(document.querySelectorAll("input[type=number]"));
        const currentIndex = numberInputs.indexOf(activeElement);

        if (event.key === "ArrowDown") {
            const nextIndex = currentIndex === numberInputs.length - 1 ? 0 : currentIndex + 1;
            numberInputs[nextIndex].focus();
        } else {
            const previousIndex = currentIndex === 0 ? numberInputs.length - 1 : currentIndex - 1;
            numberInputs[previousIndex].focus();
        }
    }
});