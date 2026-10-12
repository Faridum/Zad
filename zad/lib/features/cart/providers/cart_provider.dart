import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/cart_item_model.dart';
import '../../home/models/product_model.dart';


final cartProvider =
StateNotifierProvider<CartNotifier, List<CartItem>>(
      (ref) => CartNotifier(),
);


class CartNotifier extends StateNotifier<List<CartItem>> {
  CartNotifier() : super([]);


  int? get currentRestaurantId {
    if (state.isEmpty) return null;
    return state.first.product.restaurantId;
  }


  bool canAddProduct(Product product) {
    if (state.isEmpty) return true;
    return product.restaurantId == currentRestaurantId;
  }


  void clearCart() {
    state = [];
  }


  void addProduct(Product product, int quantity) {
    final index =
    state.indexWhere((item) => item.product.id == product.id);


    if (index >= 0) {
      state = [
        for (int i = 0; i < state.length; i++)
          if (i == index)
            CartItem(
              product: state[i].product,
              quantity: state[i].quantity + quantity,
            )
          else
            state[i],
      ];
    } else {
      state = [
        ...state,
        CartItem(product: product, quantity: quantity),
      ];
    }
  }


  void removeProduct(Product product) {
    state =
        state.where((item) => item.product.id != product.id).toList();
  }


  double get totalAmount {
    return state.fold(
      0,
          (sum, item) => sum + item.totalPrice,
    );
  }
  void decreaseQuantity(Product product) {
    state = [
      for (final item in state)
        if (item.product.id == product.id)
          if (item.quantity > 1)
            CartItem(
              product: item.product,
              quantity: item.quantity - 1,
            )
          else
            item
        else
          item
    ];
  }


}

