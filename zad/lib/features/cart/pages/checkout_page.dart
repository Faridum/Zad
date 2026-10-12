import 'dart:convert'; // ✅ مهم جدًا
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';


import '../../orders/services/order_service.dart';
import '../providers/cart_provider.dart';
import '../../auth/providers/user_profile_provider.dart';
import '../../../core/theme/app_theme.dart';


enum PaymentMethod { cash, card }


class CheckoutPage extends ConsumerStatefulWidget {
  const CheckoutPage({super.key});


  @override
  ConsumerState<CheckoutPage> createState() => _CheckoutPageState();
}


class _CheckoutPageState extends ConsumerState<CheckoutPage> {
  final _streetController = TextEditingController();
  final _cityController = TextEditingController();
  final _notesController = TextEditingController();


  PaymentMethod _paymentMethod = PaymentMethod.cash;


  @override
  void dispose() {
    _streetController.dispose();
    _cityController.dispose();
    _notesController.dispose();
    super.dispose();
  }


  @override
  Widget build(BuildContext context) {
    final cartItems = ref.watch(cartProvider);
    final cartNotifier = ref.read(cartProvider.notifier);
    final userAsync = ref.watch(userProfileProvider);


    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.pop(),
        ),
        title: const Text('Checkout'),
        centerTitle: true,
      ),
      body: cartItems.isEmpty
          ? const Center(child: Text('Your cart is empty'))
          : userAsync.when(
        loading: () =>
        const Center(child: CircularProgressIndicator()),
        error: (_, __) =>
        const Center(child: Text('Failed to load user data')),
        data: (user) {
          final userName = user['name'] ?? '';
          final userPhone = user['phone'] ?? '';


          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Order Summary',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),


                ...cartItems.map((item) {
                  return Container(
                    margin: const EdgeInsets.only(bottom: 10),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.05),
                          blurRadius: 6,
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            '${item.product.name} ×${item.quantity}',
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                        Text(
                          '${item.totalPrice.toStringAsFixed(2)} AED',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  );
                }),


                const SizedBox(height: 24),


                const Text(
                  'Delivery Address',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),


                _inputField(
                  label: 'Full Name',
                  initialValue: userName,
                  icon: Icons.person_outline,
                  enabled: false,
                ),
                const SizedBox(height: 12),


                _inputField(
                  label: 'Phone Number',
                  initialValue: userPhone,
                  icon: Icons.phone_outlined,
                  enabled: false,
                ),
                const SizedBox(height: 12),


                _inputField(
                  controller: _streetController,
                  label: 'Street Address',
                  icon: Icons.location_on_outlined,
                ),
                const SizedBox(height: 12),


                _inputField(
                  controller: _cityController,
                  label: 'City',
                  icon: Icons.location_city_outlined,
                ),
                const SizedBox(height: 12),


                _inputField(
                  controller: _notesController,
                  label: 'Additional Notes (optional)',
                  icon: Icons.note_outlined,
                ),


                const SizedBox(height: 24),


                const Text(
                  'Payment Method',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),


                RadioListTile<PaymentMethod>(
                  value: PaymentMethod.cash,
                  groupValue: _paymentMethod,
                  onChanged: (val) =>
                      setState(() => _paymentMethod = val!),
                  title: const Text('Cash on Delivery'),
                  activeColor: AppTheme.primary,
                ),


                const SizedBox(height: 24),


                Row(
                  children: [
                    const Text(
                      'Total',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const Spacer(),
                    Text(
                      '${cartNotifier.totalAmount.toStringAsFixed(2)} AED',
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.primary,
                      ),
                    ),
                  ],
                ),


                const SizedBox(height: 32),


                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: () async {
                      if (_streetController.text.isEmpty ||
                          _cityController.text.isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                              content:
                              Text("Please enter address")),
                        );
                        return;
                      }


                      final restaurantId =
                      cartItems.first.product.restaurantId!;


                      // ✅ 1. تجهيز items
                      final items = cartItems.map((item) {
                        return {
                          "product_id": item.product.id,
                          "quantity": item.quantity,
                        };
                      }).toList();


                      // ✅ 2. تحويلها إلى JSON String
                      final itemsJson = jsonEncode(items);


                      final address =
                          "${_streetController.text}, ${_cityController.text}";


                      try {
                        await OrderService.createOrder(
                          restaurantId: restaurantId,
                          items: items,
                          address: address,
                          paymentMethod: _paymentMethod.name, // cash / card
                          notes: _notesController.text,
                        );




                        ref.read(cartProvider.notifier).clearCart();
                        if (!mounted) return;
                        context.go('/order_success');
                      } catch (e) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text(
                              e.toString().replaceAll('Exception: ', ''),
                            ),
                          ),
                        );
                      }


                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primary,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                    child: const Text(
                      'Place Order',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }


  Widget _inputField({
    TextEditingController? controller,
    String? initialValue,
    required String label,
    required IconData icon,
    bool enabled = true,
  }) {
    return TextFormField(
      controller: controller,
      initialValue: controller == null ? initialValue : null,
      enabled: enabled,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon),
        filled: true,
        fillColor:
        enabled ? AppTheme.inputFill : Colors.grey.shade200,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide.none,
        ),
      ),
    );
  }
}

