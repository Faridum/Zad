import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';


import '../../home/models/product_model.dart';
import '../../../core/theme/app_theme.dart';
import '../../cart/providers/cart_provider.dart';


class ProductDetailsPage extends ConsumerStatefulWidget {
  final Product product;


  const ProductDetailsPage({
    super.key,
    required this.product,
  });


  @override
  ConsumerState<ProductDetailsPage> createState() =>
      _ProductDetailsPageState();
}


class _ProductDetailsPageState
    extends ConsumerState<ProductDetailsPage> {
  int quantity = 1;


  double get unitPrice => widget.product.price;
  double get totalPrice => unitPrice * quantity;


  void _increase() {
    setState(() => quantity++);
  }


  void _decrease() {
    if (quantity > 1) {
      setState(() => quantity--);
    }
  }


  @override
  Widget build(BuildContext context) {
    final product = widget.product;


    return Scaffold(
      appBar: AppBar(
        title: const Text('Product Details'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ---------------- IMAGE ----------------
            ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: AspectRatio(
                aspectRatio: 1.2,
                child: product.photo != null && product.photo!.isNotEmpty
                    ? Image.network(
                  product.photo!,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) =>
                      _imagePlaceholder(),
                )
                    : _imagePlaceholder(),
              ),
            ),


            const SizedBox(height: 20),


            // ---------------- NAME ----------------
            Text(
              product.name,
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
              ),
            ),


            const SizedBox(height: 8),


            // ---------------- UNIT PRICE (FIXED) ----------------
            Text(
              '${unitPrice.toStringAsFixed(2)} AED',
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              ),
            ),


            const SizedBox(height: 24),


            // ---------------- QUANTITY ----------------
            Row(
              children: [
                const Text(
                  'Quantity',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const Spacer(),
                IconButton(
                  onPressed: _decrease,
                  icon: const Icon(Icons.remove_circle_outline),
                ),
                Text(
                  quantity.toString(),
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                IconButton(
                  onPressed: _increase,
                  icon: const Icon(Icons.add_circle_outline),
                ),
              ],
            ),


            const SizedBox(height: 12),


            // ---------------- TOTAL ----------------
            Row(
              children: [
                const Text(
                  'Total',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const Spacer(),
                Text(
                  '${totalPrice.toStringAsFixed(2)} AED',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),


            const SizedBox(height: 32),


            // ---------------- ADD TO CART ----------------
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                onPressed: () async {
                  final cartNotifier = ref.read(cartProvider.notifier);


                  if (!cartNotifier.canAddProduct(product)) {
                    final confirm = await showDialog<bool>(
                      context: context,
                      builder: (context) => AlertDialog(
                        title: const Text("Reset Cart?"),
                        content: const Text(
                          "Your cart contains items from another restaurant.\n"
                              "If you continue, your cart will be cleared.",
                        ),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(context, false),
                            child: const Text("Cancel"),
                          ),
                          ElevatedButton(
                            onPressed: () => Navigator.pop(context, true),
                            child: const Text("Continue"),
                          ),
                        ],
                      ),
                    );


                    if (confirm != true) return;


                    cartNotifier.clearCart();
                  }


                  cartNotifier.addProduct(product, quantity);


                  Navigator.pop(context);
                },


                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primary,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
                child: const Text(
                  'Add to Cart',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }


  Widget _imagePlaceholder() {
    return Container(
      color: AppTheme.inputFill,
      child: const Center(
        child: Icon(
          Icons.fastfood,
          size: 48,
          color: AppTheme.greyLabel,
        ),
      ),
    );
  }
}

