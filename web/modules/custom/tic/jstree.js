$(function () {
  // 6 create an instance when the DOM is ready
  $('#jstree').jstree();
  // 7 bind to events triggered on the tree
  $('#jstree').on("changed.jstree", function (e, data) {
    console.log(data.selected);
  });
  // 8 interact with the tree - either way is OK
  $('button').on('click', function () {
    $('#jstree').jstree(true).select_node('child_node_1');
    $('#jstree').jstree('select_node', 'child_node_1');
    $.jstree.reference('#jstree').select_node('child_node_1');
  });

  $('#using_json_2').jstree({
    'core': {
      'data': [
        {"id": "ajson1", "parent": "#", "text": "Simple root node"},
        {"id": "ajson2", "parent": "#", "text": "Root node 2"},
        {"id": "ajson3", "parent": "ajson2", "text": "Child 1"},
        {"id": "ajson4", "parent": "ajson2", "text": "Child 2"},
      ]
    }
  });

  $('#ajax').jstree({
    'core': {
      'data': {
        'url': function (node) {
          return node.id === '#' ?
            '/tic/ajax_roots.json' :
            '/tic/ajax_children.json';
        },
        'data': function (node) {
          return {'id': node.id};
        }
      }
    }
  });

});
