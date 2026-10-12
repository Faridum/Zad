// lib/features/orders/services/order_service.dart
import 'dart:convert';
import '../../../shared/services/api_service.dart';


class OrderService {
  static Future<void> createOrder({
    required int restaurantId,
    required List<Map<String, dynamic>> items,
    required String address,
    required String paymentMethod, // ✅
    String? notes,
  }) async {
    final response = await ApiService.post(
      'orders/create.php',
      body: {
        'restaurant_id': restaurantId,
        'payment_method': paymentMethod, // ✅ مهم جدًا
        'address': address,
        'items': items, // JSON حقيقي
        if (notes != null && notes.isNotEmpty) 'notes': notes,
      },
    );


    if (!response.success) {
      throw Exception(response.message);
    }
  }
}



