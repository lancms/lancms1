/**
 * @param {String} HTML representing any number of sibling elements
 * @return {NodeList}
 */
export function parseFromHtml(html) {
    var template = document.createElement('template');
    template.innerHTML = html;
    return template.content.childNodes;
}

export function insertAfter(el, referenceNode) {
  referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
}
