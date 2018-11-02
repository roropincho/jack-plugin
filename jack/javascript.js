function hideCurrent(num) {
    var current = document.getElementById("volet-" + num);
    current.setAttribute("class", current.getAttribute("class").replace(" volet-visible", ""));

    return current;
}

function showVolet(num) {
    var volet = document.getElementById("volet-" + num);
    volet.setAttribute("class", volet.getAttribute("class") + " volet-visible");
}

function putBackgroundBack(inputTemp) {
    inputTemp.style.backgroundColor = "";
}

function putParentBackgroundBack(inputTemp) {
    inputTemp.parentElement.style.backgroundColor = "";
}

function goToPrev(num) {
    hideCurrent(num);
    showVolet(num - (num % 2 == 0 ? 1 : 2));
}

function goToNext(num) {
    var current = hideCurrent(num);
    var allAnswers = current.querySelectorAll("input[type='radio']:checked");
    var next = num;

    for (var i = 0; i < allAnswers.length; i++) {
        var inner = allAnswers[i].nextElementSibling.innerHTML;

        if (inner == "Oui" || inner == "Yes") {
            next += 1;
        }
        else if (inner == "Non" || inner == "No") {
            next += 2;
        }

        if (next != num) {
            break;
        }
    }

    showVolet(next);
}

function send(num) {
    var emailContent = "";

    for (var i = num; i > 0; i -= (1 + (i % 2))) {
        var voletTemp = document.getElementById("volet-" + i);
        var questionList = voletTemp.querySelectorAll("h3");

        for (var j = questionList.length - 1; j >= 0; j--) {
            var questionTemp = questionList[j];
            var repTemp = "";
            var radioList = voletTemp.querySelectorAll("h3:nth-of-type(" + (j + 1) + ") + .same-line input[type=radio]");
            var inputTextList = voletTemp.querySelectorAll("h3:nth-of-type(" + (j + 1) + ") + div input[type=text]");
            var radioChecked = null;

            for (var k = 0; k < radioList.length; k++) {
                var radioTemp = radioList[k];

                if (radioTemp.checked) {
                    radioChecked = radioTemp;
                }
            }

            if (radioChecked != null) {
                repTemp += radioChecked.nextElementSibling.innerHTML;
            }
            else if (radioList.length > 0) {
                questionTemp.nextElementSibling.style.backgroundColor = "rgba(234, 84, 54, 0.25)";
                return;
            }

            for (var k = 0; k < inputTextList.length; k++) {
                var inputTemp = inputTextList[k];

                if (inputTemp.value.trim() == "") {
                    inputTemp.style.backgroundColor = "rgba(234, 84, 54, 0.25)";
                    return;
                }
                else {
                    var labelTemp = inputTemp.previousElementSibling.innerHTML.replace(' <span class="oblig">*</span>', "");
                    repTemp += "\n   " + labelTemp + " : " + inputTemp.value;
                }
            }

            if (repTemp != "") {
                repTemp = questionTemp.innerHTML + " : " + repTemp;
            }

            emailContent = repTemp + "\n\n" + emailContent;
        }
    }

    emailContent = "Voici toutes les informations fournies:\n\n" + emailContent;

    console.log(emailContent);

    var link = "mailto:" + document.getElementById("volet-container").getAttribute("data-email")
                + "?subject=" + escape("Nouvelle demande de création de Prêt à vivre")
                + "&body=" + escape(emailContent);
    
    window.location.href = link;
}
