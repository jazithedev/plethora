/**
 * @author	Krzysztof Trzos
 * @class	Framework
 * @returns	{Framework}
 */
function Framework() {
}

/**
 * @public
 * @returns {Number}
 */
Framework.prototype.getPageWidth = function() {
	return (window.innerWidth > 0) ? window.innerWidth : screen.width;
};

// initialize instance
var oFramework = new Framework();