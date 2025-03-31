<?php
// 定义二叉树节点类
class TreeNode {
    public $value;
    public $left;
    public $right;

    public function __construct($value) {
        $this->value = $value;
        $this->left = null;
        $this->right = null;
    }
}
$myTreeNode = new TreeNode('root');

echo $myTreeNode->value.PHP_EOL;
echo $myTreeNode->left.PHP_EOL;
echo $myTreeNode->right.PHP_EOL;
echo "SUCC_".PHP_EOL;


// 定义二叉搜索树类
class BinarySearchTree {
    public $root;

    public function __construct() {
        $this->root = null;
    }

    // 插入节点
    public function insert($value) {
        $newNode = new TreeNode($value);
        if ($this->root === null) {
            $this->root = $newNode;
        } else {
            $this->insertNode($this->root, $newNode);
        }
    }

    private function insertNode(&$node, $newNode) {
        if ($newNode->value < $node->value) {
            if ($node->left === null) {
                $node->left = $newNode;
            } else {
                $this->insertNode($node->left, $newNode);
            }
        } else {
            if ($node->right === null) {
                $node->right = $newNode;
            } else {
                $this->insertNode($node->right, $newNode);
            }
        }
    }

    // 查找节点
    public function search($value) {
        return $this->searchNode($this->root, $value);
    }

    private function searchNode($node, $value) {
        if ($node === null || $node->value === $value) {
            return $node;
        }
        if ($value < $node->value) {
            return $this->searchNode($node->left, $value);
        }
        return $this->searchNode($node->right, $value);
    }

    // 删除节点
    public function delete($value) {
        $this->root = $this->deleteNode($this->root, $value);
    }

    private function deleteNode($node, $value) {
        if ($node === null) {
            return $node;
        }
        if ($value < $node->value) {
            $node->left = $this->deleteNode($node->left, $value);
        } elseif ($value > $node->value) {
            $node->right = $this->deleteNode($node->right, $value);
        } else {
            // 节点只有一个子节点或没有子节点
            if ($node->left === null) {
                return $node->right;
            } elseif ($node->right === null) {
                return $node->left;
            }
            // 节点有两个子节点，找到右子树的最小节点
            $temp = $this->minValueNode($node->right);
            $node->value = $temp->value;
            $node->right = $this->deleteNode($node->right, $temp->value);
        }
        return $node;
    }

    private function minValueNode($node) {
        $current = $node;
        while ($current->left!== null) {
            $current = $current->left;
        }
        return $current;
    }

    // 修改节点值
    public function update($oldValue, $newValue) {
        $this->delete($oldValue);
        $this->insert($newValue);
    }

    // 中序遍历二叉树（用于验证操作结果）
    public function inorderTraversal($node) {
        if ($node!== null) {
            $this->inorderTraversal($node->left);
            echo $node->value. " ";
            $this->inorderTraversal($node->right);
        }
    }
}

// 使用示例
$bst = new BinarySearchTree();

// 插入节点
$bst->insert(50);
$bst->insert(30);
$bst->insert(60);
/*$bst->insert(40);
$bst->insert(70);
$bst->insert(60);
$bst->insert(80);*/

// 中序遍历
echo "插入节点后的中序遍历结果: ";
$bst->inorderTraversal($bst->root);
echo "\n";

// 查找节点
$searchResult = $bst->search(40);
if ($searchResult!== null) {
    echo "找到节点: ". $searchResult->value. "\n";
} else {
    echo "未找到节点\n";
}

// 删除节点
$bst->delete(30);
echo "删除节点 30 后的中序遍历结果: ";
$bst->inorderTraversal($bst->root);
echo "\n";

// 修改节点值
$bst->update(40, 45);
echo "将节点 40 修改为 45 后的中序遍历结果: ";
$bst->inorderTraversal($bst->root);
echo "\n";
?>