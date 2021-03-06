<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code)
    {
        $CI = &get_instance();
        if($CI->Orderitems->exists($num, $code))
        {
            //adding items to a existing order
            $record = $CI->Orderitems->get($num, $code);
            $record->quantity++;
            $CI->Orderitems->update($record);
        }
        else
        {
            //staring a new order.
            $record = $CI->Orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->Orderitems->add($record);
        }
    }

    // calculate the total for an order
    function total($num)
    {
       $CI = &get_instance();
       $CI->load->model('OrderItems');
       
       $items = $this->Orderitems->some('order', $num);
       
       $result = 0;
       foreach($items as $item)
       {
           $menuitem = $this->Menu->get($item->item);
           $result += $item->quantity * $menuitem->price;
       }
       
       //used originnaly but to update the amount it was not working as intended
       $money_format = sprintf("$%.2f", $result);
       return $result;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        
        $CI = &get_instance();
        $items = $CI->Orderitems->group($num);
        $goten = array();
        
        if(count($items) > 0)
        {
            //go through all the items in the order
            foreach($items as $item)
            {
                $menu = $CI->Menu->get($item->item);
                $goten[$menu->category] = 1;
            }
        }
        
        // if the order has an item from every category
        return isset($goten['m']) && isset($goten['d']) && isset($goten['s']);
    }

}
