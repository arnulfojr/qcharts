/**
 * @param formId
 * @param inputName
 * @param modalId
 * @constructor
 */
var CronController = function(formId, inputName, modalId) {
    ObjectBuilder.call(this, formId);
    this.inputName = (inputName == undefined) ? "" : inputName;
    this.cron = undefined;
    this.input = undefined;
    this.cronId = "cron_id";
    this.modalController = new ModalController(modalId);
    this.stateInput = undefined;
    this.useState = false;
    this.container = undefined;
};

CronController.prototype = Object.create(ObjectBuilder.prototype);
CronController.prototype.constructor = CronController;

CronController.prototype.init = function(stateInput) {

    this.isInit = true;

    var store = this;

    var options = {
        initial: "*/5 * * * *",
        customValues: {
            "Each 5 min.": "*/5 * * * *",
            "Each 15 min.": "0 0/15 * 1/1 *",
            "Each 30 min.": "0 0/30 * * *"
        },
        onChange: function() {
            var value = $(store.input).val();
            console.log(value);
            if (!store.isInit || value == "") {
                console.log("onChange");
                console.log($(this).cron("value"));
                store.setInput($(this).cron("value"));
            }
        }
    };

    $(document).ready(function() {
        store.addExtrasToForm();
        store.setStateInput(stateInput);
        console.log(store.input.value);
        if (store.input.val() != undefined && store.input.val() != "") {
            //set the initial value!
            options["initial"] = store.input.val();
        }
        store.cron = $("#" + store.cronId).cron(options);
        store.cron.cron("value", store.input.val());
        store.cron.find("select").addClass("form-control");
        if (store.useState) {
            //hide it
            store.toggle(store.stateInput.value);
        }

        store.isInit = false;

    });

};

CronController.prototype.disableStateInput = function() {
    this.useState = false;
};

/**
 * @param value
 */
CronController.prototype.toggle = function (value) {
    var store = this;
    $(store.container).removeClass("fadeInDown fadeOutUp animated");
    if (value >= 1) {
        //ok show it!
        $(store.container).addClass("fadeInDown animated");
        $(store.container).prop("disabled", false);
        return;
    }
    //hide it
    $(store.container).addClass("fadeOutUp animated");
    $(store.container).prop("disabled", true);
};

/**
 * @param inputId
 */
CronController.prototype.setStateInput = function(inputId) {
    this.stateInput = document.getElementById(inputId);
    console.log(this);

    this.useState = true;
    var store = this;
    this.stateInput.addEventListener("change", function() {
        console.log(this.value);
        store.toggle(this.value);
    });
    console.log(this.stateInput);
};

/**
 * @param newValue
 */
CronController.prototype.setInput = function(newValue) {
    console.log("updated");
    this.input.val(newValue);
};

CronController.prototype.addExtrasToForm = function() {
    var queryInput = "#" + this.formId + " input[name='" + this.inputName + "']";
    console.log(queryInput);
    this.input = $(queryInput);
    this.container = this.createColumn("col-sm-12");
    this.container.setAttribute("id", this.cronId);
    $(this.container).insertBefore(this.input);
};