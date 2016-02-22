/**
 * @param containerId
 * @param service
 * @param baseApiUrl
 * @param options
 * @constructor
 */
var GraphComparator = function (containerId, service, baseApiUrl, options) {
    Communicator.call(this, baseApiUrl, service, options);
};

GraphComparator.prototype = Object.create(Communicator.prototype);
GraphComparator.prototype.constructor = GraphComparator;

GraphComparator.prototype.initialize = function() {
    this.init(function() {
        //ok

    });
};