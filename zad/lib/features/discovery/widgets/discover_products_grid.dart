import 'package:flutter/material.dart';


import '../../../shared/widgets/product_card.dart';
import '../../home/models/product_model.dart';
import '../../product/pages/product_details_page.dart';


class DiscoverProductsGrid extends StatelessWidget {
  final List<Product> products;


  const DiscoverProductsGrid({
    super.key,
    required this.products,
  });


  @override
  Widget build(BuildContext context) {
    if (products.isEmpty) {
      return const Center(
        child: Text(
          'No products available',
          style: TextStyle(color: Colors.grey),
        ),
      );
    }


    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        mainAxisSpacing: 16,
        crossAxisSpacing: 16,
        childAspectRatio: 0.65,
      ),
      itemCount: products.length,
      itemBuilder: (context, index) {
        final product = products[index];


        return GestureDetector(
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => ProductDetailsPage(product: product),
              ),
            );
          },
          child: ProductCard(
            product: product,
          ),
        );
      },
    );
  }
}

