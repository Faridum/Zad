import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';
import '../../features/home/models/product_model.dart';


class ProductCard extends StatelessWidget {
  final Product product;
  final VoidCallback? onBuyTap;


  const ProductCard({
    super.key,
    required this.product,
    this.onBuyTap,
  });


  @override
  Widget build(BuildContext context) {
    return Container(
      width: 200,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _imageSection(),
          Padding(
            padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
            child: _infoSection(),
          ),
        ],
      ),
    );
  }


  // ---------------- IMAGE ----------------
  Widget _imageSection() {
    return ClipRRect(
      borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
      child: SizedBox(
        height: 110, // 🔒 ارتفاع ثابت يمنع أي overflow
        width: double.infinity,
        child: product.photo != null && product.photo!.isNotEmpty
            ? Image.network(
          product.photo!,
          fit: BoxFit.cover,
          errorBuilder: (_, __, ___) => _imagePlaceholder(),
        )
            : _imagePlaceholder(),
      ),
    );
  }


  Widget _imagePlaceholder() {
    return Container(
      color: AppTheme.inputFill,
      alignment: Alignment.center,
      child: const Icon(
        Icons.fastfood,
        size: 40,
        color: AppTheme.greyLabel,
      ),
    );
  }


  // ---------------- INFO ----------------
  Widget _infoSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // اسم المنتج
        Text(
          product.name,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w700,
          ),
        ),
        const SizedBox(height: 6),


        // السعر
        Text(
          '${product.price.toStringAsFixed(2)} AED',
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 10),


        // زر Buy
        Align(
          alignment: Alignment.centerRight,
          child: SizedBox(
            height: 34, // 🔒 ارتفاع ثابت
            child: ElevatedButton(
              onPressed: onBuyTap,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primary,
                elevation: 0,
                padding: const EdgeInsets.symmetric(horizontal: 18),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(20),
                ),
              ),
              child: const Text(
                'Buy',
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

