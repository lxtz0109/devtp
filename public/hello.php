<?php
// 定义树节点类
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

// 深度优先搜索（前序遍历）
function dfsPreorder($node) {
    if ($node === null) {
        return;
    }
    // 处理当前节点
    echo $node->value . " ";
    // 递归处理左子树
    dfsPreorder($node->left);
    // 递归处理右子树
    dfsPreorder($node->right);
}

// 构建一个简单的二叉树
$root = new TreeNode(1);
$root->left = new TreeNode(2);
$root->right = new TreeNode(3);
$root->left->left = new TreeNode(4);
$root->left->right = new TreeNode(5);
$root->left->left->left = new TreeNode(9);
$root->left->left->right = new TreeNode(12);

// 调用深度优先搜索进行前序遍历
dfsPreorder($root);
?>