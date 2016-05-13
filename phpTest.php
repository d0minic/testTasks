<?php

abstract class Tree {
    // создает узел (если $parentNode == NULL - корень)
    abstract protected function createNode(Node $node,$parentNode=NULL);

    // удаляет узел и все дочерние узлы
    abstract protected function deleteNode(Node $node);

    // один узел делает дочерним по отношению к другому
    abstract protected function attachNode(Node $node,Node $parent);

    // получает узел по названию
    abstract protected function getNode($nodeName);

    // преобразует дерево со всеми элементами в ассоциативный массив
    abstract protected function export();
}

class Node {

    private $name;
    private $_children = array();

    function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getChildren() {
        return $this->_children;
    }

    public function addChild($child) {
        $this->_children[] = $child;
    }

    public function deleteChild($key) {
        if (isset($this->_children[$key]))
            unset($this->_children[$key]);
    }
}

class TreeExt extends Tree {

    private $root;

    public function createNode(Node $node, $parentNode = NULL) {
        if (is_null($parentNode)) {
            $this->root = $node;
        } else {
            $parentNode->addChild($node);
        }
    }

    public function getNode($nodeName) {
        return $this->recSearch($this->root, $nodeName);
    }

    public function deleteNode(Node $node) {
        if ($this->root == $node)
            unset($this->root);
        else
            $this->recDeleteNode($this->root, $node);
    }

    private function recDeleteNode(Node $parent, Node $node) {
        foreach ($parent->getChildren() as $key => $child) {
            if ($child == $node) {
                $parent->deleteChild($key);
                return;
            } else {
                $this->recDeleteNode($child, $node);
            }
        }
    }

    private function recSearch($node, $nodeName) {
        if ($node->getName() == $nodeName) {
            return $node;
        } else {
            foreach ($node->getChildren() as $child) {
                if ($child->getName() == $nodeName) {
                    return $child;
                } else {
                    $retval = $this->recSearch($child, $nodeName);
                    if (is_null($retval) == false) return $retval;
                }
            }
        }
        return null; // если ничего не нашли в данной ветке, вернём null
    }

    public function attachNode(Node $node, Node $parent) {
        $this->deleteNode($node);
        $parent->addChild($node);
    }

    public function export() {
        if (is_null($this->root) == true) return;
        $res = array();
        $res[$this->root->getName()] = $this->read($this->root);
        return $res;
    }

    private function read(Node $node) {
        $res = array();
        foreach ($node->getChildren() as $child) {
            $res[$child->getName()] = $this->read($child);
        }
        return $res;
    }
}

$tree = new TreeExt();


// 1. создать корень country
$tree->createNode(new Node('country'));
// 2. создать в нем узел kiev
$tree->createNode(new Node('kiev'), $tree->getNode('country'));
// 3. в узле kiev создать узел kremlin
$tree->createNode(new Node('kremlin'), $tree->getNode('kiev'));
// 4. в узле kremlin создать узел house
$tree->createNode(new Node('house'), $tree->getNode('kremlin'));
// 5. в узле kremlin создать узел tower
$tree->createNode(new Node('tower'), $tree->getNode('kremlin'));
// 4. в корневом узле создать узел moskow
$tree->createNode(new Node('moskow'), $tree->getNode('country'));
// 5. сделать узел kremlin дочерним узлом у moskow
$tree->attachNode($tree->getNode('kremlin'), $tree->getNode('moskow'));
// 6. в узле kiev создать узел maidan
$tree->createNode(new Node('maidan'), $tree->getNode('kiev'));
// 7. удалить узел kiev
$tree->deleteNode($tree->getNode('kiev'));
// 8. получить дерево в виде массива, сделать print_r
print_r($tree->export());