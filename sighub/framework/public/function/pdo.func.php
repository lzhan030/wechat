<?php
/**
 * Pdo操作
 *
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 执行一条非查询语句
 *
 * @param string $sql
 * @param array or string $params
 * @return mixed
 *		  成功返回受影响的行数
 *		  失败返回FALSE
 */
function pdo_query($sql, $params = array()) {
	global $wpdb;
	if($params)
		return $wpdb->query( $wpdb->prepare( $sql,$params ) );
	else
		return $wpdb->query( $sql );
	
}

/**
 * 执行SQL返回第一个字段
 *
 * @param string $sql
 * @param array $params
 * @param int $column 返回查询结果的某列，默认为第一列
 * @return mixed
 */
function pdo_fetchcolumn($sql, $params = array(), $column = 0) {
	global $wpdb;
	return $wpdb->get_col( $wpdb->prepare( $sql,$params ), $column );
}
/**
 * 执行SQL返回第一行
 *
 * @param string $sql
 * @param array $params
 * @return mixed
 */
function pdo_fetch($sql, $params = array()) {
	global $wpdb;
	if($params)
		return $wpdb->get_row($wpdb->prepare( $sql,$params ), ARRAY_A, 0);
	else
		return $wpdb->get_row( $sql , ARRAY_A, 0);
}
/**
 * 执行SQL返回全部记录
 *
 * @param string $sql
 * @param array $params
 * @return mixed
 */
function pdo_fetchall($sql, $params = array(), $keyfield = '') {
	global $wpdb;
	if(empty($keyfield))
		return $wpdb->get_results(empty($params)?$sql:$wpdb->prepare( $sql,$params ), ARRAY_A);
	else{
		$temp = $wpdb->get_results(empty($params)?$sql:$wpdb->prepare( $sql,$params ), ARRAY_A);
		$rs = array();
		if (!empty($temp)) {
				foreach ($temp as $key => &$row) {
					if (isset($row[$keyfield])) {
						$rs[$row[$keyfield]] = $row;
					} else {
						$rs[] = $row;
					}
				}
			}
	}
}

/**
 * 更新记录
 *
 * @param string $table
 * @param array $data
 *		  要更新的数据数组
 *		  array(
 *			  '字段名' => '值'
 *		  )
 * @param array $params
 *		  更新条件
 *		  array(
 *			  '字段名' => '值'
 *		  )
 * @param string $gule
 *		  可以为AND OR
 * @return mixed
 */
function pdo_update($table, $data = array(), $params = array(), $gule = 'AND') {
	global $wpdb;
	return $wpdb->update( $wpdb->prefix.$table, $data, $params, null, null );
}

/**
 * 更新记录
 *
 * @param string $table
 * @param array $data
 *		  要更新的数据数组
 *		  array(
 *			  '字段名' => '值'
 *		  )
 * @param boolean $replace
 *		  是否执行REPLACE INTO
 *		  默认为FALSE
 * @return mixed
 */
function pdo_insert($table, $data = array(), $replace = FALSE) {
	global $wpdb;
	if(!$replace)
		return $wpdb->insert( $wpdb->prefix.$table, $data);
	else
		return $wpdb->replace( $wpdb->prefix.$table, $data);
	
}

/**
 * 删除记录
 *
 * @param string $table
 * @param array $params
 *		  更新条件
 *		  array(
 *			  '字段名' => '值'
 *		  )
 * @param string $gule
 *		  可以为AND OR
 * @return mixed
 */
function pdo_delete($table, $params = array(), $gule = 'AND') {
	global $wpdb;
	return $wpdb->delete( $wpdb->prefix.$table, $params );
}

/**
 * 返回lastInsertId
 *
 */
function pdo_insertid() {
	global $wpdb;
	return $wpdb -> insert_id;
}

function tablename($t){
	global $wpdb;
	return $wpdb -> prefix.$t;
}
