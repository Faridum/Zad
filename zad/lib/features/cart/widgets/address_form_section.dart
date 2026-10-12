import 'package:flutter/material.dart';

class AddressFormSection extends StatelessWidget {
  final TextEditingController streetController;
  final TextEditingController cityController;
  final TextEditingController notesController;

  const AddressFormSection({
    super.key,
    required this.streetController,
    required this.cityController,
    required this.notesController,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Delivery Address',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 16),

        // Street
        TextField(
          controller: streetController,
          decoration: const InputDecoration(
            labelText: 'Street Address',
            hintText: 'e.g. King Hussein St',
            border: OutlineInputBorder(),
          ),
        ),

        const SizedBox(height: 12),

        // City
        TextField(
          controller: cityController,
          decoration: const InputDecoration(
            labelText: 'City',
            hintText: 'e.g. Amman',
            border: OutlineInputBorder(),
          ),
        ),

        const SizedBox(height: 12),

        // Notes
        TextField(
          controller: notesController,
          maxLines: 2,
          decoration: const InputDecoration(
            labelText: 'Building / Notes (optional)',
            hintText: 'Apartment, floor, landmark...',
            border: OutlineInputBorder(),
          ),
        ),
      ],
    );
  }
}
