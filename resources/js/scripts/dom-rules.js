;(function ($) {

    let defaults = {
        parentSelector: '',
        scopeSelector: '',
        rules: []
    };

    let CreateDomRules = function (options) {

        this.options = $.extend({}, defaults, options);
        this.conditions = ['==', '===', '!=', '!==', '>', '>=', '<', '<=', 'any', 'not-any'];
        this.applyRules();

    };

    CreateDomRules.prototype.evalCallback = function (rule, condition) {

        if (condition) {

            if (rule.showTargets && typeof rule.showTargets == "function") {
                return rule.showTargets;
            } else if (this.options.showTargets && typeof this.options.showTargets == 'function') {
                return this.options.showTargets;
            } else {
                return this.showTargets;
            }

        } else {

            if (rule.hideTargets && typeof rule.hideTargets == "function") {
                return rule.hideTargets;
            } else if (this.options.hideTargets && typeof this.options.hideTargets == 'function') {
                return this.options.hideTargets;
            } else {
                return this.hideTargets;
            }

        }

    };

    CreateDomRules.prototype.runRule = function (e) {

        let
            condition = this.evalCondition(e.data.rule.condition, e.data.controller.val(), e.data.rule.value),
            callback = this.evalCallback(e.data.rule, condition);

        callback(e.data.rule, e.data.controller, condition, e.data.targets, e.data.scope);

    };

    CreateDomRules.prototype.applyRule = function (rule) {

        let
            scopeSelector = (rule.scopeSelector) ? rule.scopeSelector : this.options.scopeSelector,
            $scope = $(this.options.parentSelector).find(scopeSelector),
            that = this;

        $scope.each(function () {

            let
                $controller = $(this).find(rule.controller),
                $targets = $(this).find(rule.targets),
                data = {
                    rule: rule,
                    controller: $controller,
                    targets: $targets,
                    scope: $scope
                };

            $controller.on('change', data, that.runRule.bind(that)).trigger('change', data);

        });

    };

    CreateDomRules.prototype.showTargets = function (rule, $controller, condition, $targets) {

        $targets.show();

    };

    CreateDomRules.prototype.hideTargets = function (rule, $controller, condition, $targets) {

        $targets.hide();

    };

    CreateDomRules.prototype.evalCondition = function (condition, val1, val2) {

        if (this.conditions.indexOf(condition) > -1) {

            switch (condition) {
                case "==": {
                    return val1 === val2;
                }
                case "===": {
                    return val1 === val2;
                }
                case "!=": {
                    return val1 !== val2;
                }
                case "!==": {
                    return val1 !== val2;
                }
                case ">": {
                    return val1 > val2;
                }
                case "<": {
                    return val1 < val2;
                }
                case "any": {
                    return val2.indexOf(val1) >= 0;
                }
                case "not-any": {
                    return val2.indexOf(val1) < 0;
                }
            }

        } else {
            throw new Error("Unknown condition:" + condition);
        }

    };

    CreateDomRules.prototype.unbindEvents = function () {

        this.options.rules.forEach(function (rule) {

            $(this.options.parentSelector).find(rule.controller).off('change');

        }.bind(this));

    };

    CreateDomRules.prototype.applyRules = function () {

        this.options.rules.forEach(function (rule) {

            this.applyRule(rule);

        }.bind(this));

    };

    CreateDomRules.prototype.rulesUpdate = function () {

        this.unbindEvents();
        this.applyRules();

    };

    $.createDomRules = function (options) {

        return new CreateDomRules(options);

    }

})(jQuery);
