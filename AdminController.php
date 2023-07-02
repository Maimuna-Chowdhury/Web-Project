<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;
use App\Models\order;
use Notification;
use App\Notifications\SendEmailNotification;


class AdminController extends Controller
{
    //
    public function view_category()
    {
        $data=category::all();  //getting all the data from the category table using Category model
        return view('admin.category',compact('data')); //sending category data to category view
    }

    public function add_category(Request $request)
    {
        //storing category model in data variable
        $data=new category;
        //input name 'category' in form in category.blade.php
        $data->category_name=$request->category; 

        $data->save();
        return redirect()->back()->with('message','category added successfully');

    }

    public function delete_category($id)
    {
           $data=category::find($id);
           $data->delete();
           return redirect()->back()->with('message','Category Deleted successfully');
    }

    public function view_product()
    {
        $category=category::all();

        return view('admin.product',compact('category'));
    }

    public function add_product(Request $request)
    {
     $product=new product;
     $product->title=$request->title;
     $product->description=$request->description;
     $product->price=$request->price;
     $product->quantity=$request->quantity;
     $product->discount_price=$request->dis_price;
     $product->category=$request->category;


    
    $image=$request->image;  //storing image in the image variable
    $imagename=time().'.'.$image->getClientOriginalExtension();  //every image will have a different name bcz of time function
    $request->image->move('product',$imagename); //taking image to product folder
    $product->image=$imagename;  
     $product->save();
     return redirect()->back()->with('message','Product Added Successfully');
       
    }


    public function show_product()
    {
        $product=product::all();
        return view('admin.show_product',compact('product'));
    }

    public function order()
    {
        $order=order::all();
        return view('admin.order',compact('order'));
    }


    public function delivered($id)
    {
         $order=order::find($id);
         $order->delivery_status="Delivered";
         $order->delivery_status="Paid";
         $order->save();
         return redirect()->back();
    }

    public function send_email($id)
    {
        $order=order::find($id);
       return view('admin.email_info',compact('order')); 
    }

    public function send_user_email(Request $request,$id)
    {
        $order=order::find($id);
        $details=[
          'greeting'=>$request->greeting,
          'firstline'=>$request->firstline,
          'body'=>'Thank you for purchasing our product',
          'button'=>$request->button,
          'url'=>$request->url,
          'lastline'=>$request->lastline,
         
        ];
        //it will find email id and send email
        Notification::send($order,new SendEmailNotification($details));
        return redirect()->back();
    }
}

